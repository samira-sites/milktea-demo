console.log("script loaded");

/* =========================
   HERO ANIMATION
========================= */
const heroImage = document.querySelector(".hero-image");

if (heroImage) {
  heroImage.style.opacity = "0";
  heroImage.style.transform = "translateY(30px)";

  const revealHero = () => {
    heroImage.style.transition = "opacity .9s ease, transform .9s ease";
    heroImage.style.opacity = "1";
    heroImage.style.transform = "translateY(0)";
  };

  if (document.readyState === "complete") {
    revealHero();
  } else {
    window.addEventListener("load", revealHero);
  }
}

/* =========================
   ELEMENTS (SAFE INIT)
========================= */
const menuGrid = document.getElementById("menuGrid");
const cartButton = document.getElementById("cartButton");
const cart = document.getElementById("cart");
const cartOverlay = document.getElementById("cartOverlay");
const cartClose = document.getElementById("cartClose");
const cartItems = document.getElementById("cartItems");
const cartEmpty = document.getElementById("cartEmpty");
const cartSubtotal = document.getElementById("cartSubtotal");
const cartTotal = document.getElementById("cartTotal");
const cartCount = document.getElementById("cartCount");
const placeOrder = document.getElementById("placeOrder");
const orderModal = document.getElementById("orderModal");
const orderOk = document.getElementById("orderOk");
const burger = document.getElementById("burger");
const navMenu = document.getElementById("navMenu");
const navLinks = document.querySelectorAll(".nav-link");

/* =========================
   MENU DATA
========================= */
const drinks = [
  {
    id: 1,
    name: "Classic Milk Tea",
    price: 4.5,
    image: "assets/images/classic-milktea.webp",
    desc: "Creamy black tea with chewy pearls.",
  },
  {
    id: 2,
    name: "Taro Milk Tea",
    price: 5.2,
    image: "assets/images/taro-milktea.webp",
    desc: "Nutty taro blended into silky milk tea.",
  },
  {
    id: 3,
    name: "Brown Sugar Boba",
    price: 5.8,
    image: "assets/images/brown-sugar-boba.webp",
    desc: "Rich caramelized brown sugar swirl.",
  },
  {
    id: 4,
    name: "Matcha Latte",
    price: 5.4,
    image: "assets/images/matcha-latte.webp",
    desc: "Smooth earthy matcha with fresh milk.",
  },
  {
    id: 5,
    name: "Strawberry Fruit Tea",
    price: 4.9,
    image: "assets/images/Strawberry Fruit Tea.webp",
    desc: "Refreshing berries with fruity tea.",
  },
  {
    id: 6,
    name: "Thai Milk Tea",
    price: 5.0,
    image: "assets/images/Thai Milk Tea.webp",
    desc: "Bold Thai tea with creamy sweetness.",
  },
  {
    id: 7,
    name: "Wintermelon Tea",
    price: 4.7,
    image: "assets/images/Wintermelon Tea.webp",
    desc: "Light and sweet traditional favorite.",
  },
  {
    id: 8,
    name: "Cheese Foam Milk Tea",
    price: 5.9,
    image: "assets/images/Cheese Foam Milk Tea.webp",
    desc: "Sweet tea topped with creamy foam.",
  },
];

let cartData = [];

/* =========================
   MENU RENDER
========================= */
function renderMenu() {
  if (!menuGrid) return;

  menuGrid.innerHTML = drinks
    .map(
      (drink) => `
    <article class="menu-card">
      <div class="menu-image">
        <img src="${drink.image}" alt="${drink.name}">
      </div>

      <div class="menu-content">
        <h3>${drink.name}</h3>
        <p class="menu-desc">${drink.desc}</p>

        <div class="menu-bottom">
          <span class="price">$${drink.price.toFixed(2)}</span>

          <button class="btn btn-primary add-btn" data-id="${drink.id}">
            Add to Cart
          </button>
        </div>
      </div>
    </article>
  `
    )
    .join("");

  document.querySelectorAll(".add-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      addToCart(Number(btn.dataset.id));
    });
  });
}

/* =========================
   CART LOGIC
========================= */
function addToCart(id) {
  const item = cartData.find((i) => i.id === id);

  if (item) {
    item.qty++;
  } else {
    const drink = drinks.find((d) => d.id === id);
    cartData.push({ ...drink, qty: 1 });
  }

  updateCart();
  openCart();
}

function updateCart() {
  if (!cartItems) return;

  if (cartData.length === 0) {
    cartEmpty.style.display = "block";
    cartItems.innerHTML = "";
  } else {
    cartEmpty.style.display = "none";

    cartItems.innerHTML = cartData
      .map(
        (item) => `
      <div class="cart-item">
        <div>
          <h4>${item.name}</h4>
          <p>$${item.price.toFixed(2)}</p>
        </div>

        <div class="qty-controls">
          <button class="qty-btn minus" data-id="${item.id}">−</button>
          <span>${item.qty}</span>
          <button class="qty-btn plus" data-id="${item.id}">+</button>
        </div>
      </div>
    `
      )
      .join("");

    document.querySelectorAll(".plus").forEach((btn) => {
      btn.addEventListener("click", () => changeQty(Number(btn.dataset.id), 1));
    });

    document.querySelectorAll(".minus").forEach((btn) => {
      btn.addEventListener("click", () =>
        changeQty(Number(btn.dataset.id), -1)
      );
    });
  }

  const total = cartData.reduce((s, i) => s + i.price * i.qty, 0);
  const count = cartData.reduce((s, i) => s + i.qty, 0);

  if (cartSubtotal) cartSubtotal.textContent = `$${total.toFixed(2)}`;
  if (cartTotal) cartTotal.textContent = `$${total.toFixed(2)}`;
  if (cartCount) cartCount.textContent = count;
}

function changeQty(id, change) {
  const item = cartData.find((i) => i.id === id);
  if (!item) return;

  item.qty += change;

  if (item.qty <= 0) {
    cartData = cartData.filter((i) => i.id !== id);
  }

  updateCart();
}

/* =========================
   CART UI
========================= */
function openCart() {
  if (!cart) return;
  cart.classList.add("open");
}

function closeCart() {
  if (!cart) return;
  cart.classList.remove("open");
}

cartButton?.addEventListener("click", openCart);
cartClose?.addEventListener("click", closeCart);
cartOverlay?.addEventListener("click", closeCart);

/* =========================
   BURGER MENU
========================= */
burger?.addEventListener("click", () => {
  burger.classList.toggle("active");
  navMenu?.classList.toggle("active");

  burger.setAttribute("aria-expanded", burger.classList.contains("active"));
});

navLinks.forEach((link) => {
  link.addEventListener("click", () => {
    navMenu?.classList.remove("active");
    burger?.classList.remove("active");
  });
});

/* =========================
   ORDER FLOW (FIXED JSON BUG)
========================= */
placeOrder?.addEventListener("click", async () => {
  if (cartData.length === 0) {
    alert("Your cart is empty.");
    return;
  }

  const name = document.getElementById("customerName")?.value;
  const phone = document.getElementById("customerPhone")?.value;

  if (!name || !phone) {
    alert("Please enter name and phone.");
    return;
  }

  try {
    const res = await fetch("api/save-order.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name, phone, cart: cartData }),
    });

    const text = await res.text();

    let result;
    try {
      result = JSON.parse(text);
    } catch (e) {
      console.error("Invalid JSON from server:", text);
      alert("Server error. Check console.");
      return;
    }

    if (result.success) {
      closeCart();

      orderModal?.classList.add("show");
      orderModal?.setAttribute("aria-hidden", "false");
    } else {
      alert(result.message || "Order failed.");
    }
  } catch (err) {
    console.error(err);
    alert("Server error.");
  }
});

/* =========================
   MODAL
========================= */
function closeModal() {
  orderModal?.classList.remove("show");
  cartData = [];
  updateCart();
}

orderOk?.addEventListener("click", closeModal);

document.querySelectorAll("[data-close]").forEach((el) => {
  el.addEventListener("click", closeModal);
});

/* =========================
   INIT
========================= */
renderMenu();
updateCart();


/* =========================
   REVEAL ON SCROLL
========================= */
const revealEls = document.querySelectorAll(".reveal");

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add("active");
      observer.unobserve(entry.target); // only trigger once
    }
  });
}, { threshold: 0.1 });

revealEls.forEach((el) => observer.observe(el));