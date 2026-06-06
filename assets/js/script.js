/* HERO IMAGE ANIMATION */
console.log("script loaded");
const heroImage = document.querySelector(".hero-image");

if(heroImage){

  heroImage.style.opacity = "0";
  heroImage.style.transform = "translateY(30px)";

  window.addEventListener("load", () => {

    heroImage.style.transition =
      "opacity .9s ease, transform .9s ease";

    heroImage.style.opacity = "1";
    heroImage.style.transform = "translateY(0)";
  });
}

/* CLOSE MOBILE MENU ON DESKTOP RESIZE */

window.addEventListener("resize", () => {

  if(window.innerWidth >= 992){

    navMenu.classList.remove("active");
    burger.classList.remove("active");

    burger.setAttribute("aria-expanded","false");
  }
});

const drinks = [
  {
    id: 1,
    name: "Classic Milk Tea",
    price: 4.50,
    image: "assets/images/classic-milktea.webp",
    desc: "Creamy black tea with chewy pearls."
  },
  {
    id: 2,
    name: "Taro Milk Tea",
    price: 5.20,
    image: "assets/images/taro-milktea.webp",
    desc: "Nutty taro blended into silky milk tea."
  },
  {
    id: 3,
    name: "Brown Sugar Boba",
    price: 5.80,
    image: "assets/images/brown-sugar-boba.webp",
    desc: "Rich caramelized brown sugar swirl."
  },
  {
    id: 4,
    name: "Matcha Latte",
    price: 5.40,
    image: "assets/images/matcha-latte.webp",
    desc: "Smooth earthy matcha with fresh milk."
  },
  {
    id: 5,
    name: "Strawberry Fruit Tea",
    price: 4.90,
    image: "assets/images/Strawberry Fruit Tea.webp",
    desc: "Refreshing berries with fruity tea."
  },
  {
    id: 6,
    name: "Thai Milk Tea",
    price: 5.00,
    image: "assets/images/Thai Milk Tea.webp",
    desc: "Bold Thai tea with creamy sweetness."
  },
  {
    id: 7,
    name: "Wintermelon Tea",
    price: 4.70,
    image: "assets/images/Wintermelon Tea.webp",
    desc: "Light and sweet traditional favorite."
  },
  {
    id: 8,
    name: "Cheese Foam Milk Tea",
    price: 5.90,
    image: "assets/images/Cheese Foam Milk Tea.webp",
    desc: "Sweet tea topped with creamy foam."
  }
];

/* MENU RENDER */
function renderMenu() {
  menuGrid.innerHTML = drinks.map(drink => `
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
  `).join("");

  document.querySelectorAll(".add-btn").forEach(button => {
    button.addEventListener("click", () => {
      addToCart(Number(button.dataset.id));
    });
  });
}
  
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
  
  let cartData = [];
  
 
  /* CART */
  
  function addToCart(id){
    const item = cartData.find(item => item.id === id);
  
    if(item){
      item.qty++;
    }else{
      const drink = drinks.find(d => d.id === id);
  
      cartData.push({
        ...drink,
        qty:1
      });
    }
  
    updateCart();
    openCart();
  }
  
  function updateCart(){
  
    if(cartData.length === 0){
      cartEmpty.style.display = "block";
      cartItems.innerHTML = "";
    }else{
      cartEmpty.style.display = "none";
  
      cartItems.innerHTML = cartData.map(item => `
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
      `).join("");
  
      document.querySelectorAll(".plus").forEach(btn => {
        btn.addEventListener("click", () => {
          changeQty(Number(btn.dataset.id),1);
        });
      });
  
      document.querySelectorAll(".minus").forEach(btn => {
        btn.addEventListener("click", () => {
          changeQty(Number(btn.dataset.id),-1);
        });
      });
    }
  
    const total = cartData.reduce((sum,item)=>{
      return sum + item.price * item.qty;
    },0);
  
    const count = cartData.reduce((sum,item)=>{
      return sum + item.qty;
    },0);
  
    cartSubtotal.textContent = `$${total.toFixed(2)}`;
    cartTotal.textContent = `$${total.toFixed(2)}`;
    cartCount.textContent = count;
  }
  
  function changeQty(id,change){
    const item = cartData.find(item => item.id === id);
  
    if(!item) return;
  
    item.qty += change;
  
    if(item.qty <= 0){
      cartData = cartData.filter(item => item.id !== id);
    }
  
    updateCart();
  }
  
  /* CART OPEN/CLOSE */
  
  function openCart(){
    cart.classList.add("open");
    cart.setAttribute("aria-hidden","false");
  }
  
  function closeCart(){
    cart.classList.remove("open");
    cart.setAttribute("aria-hidden","true");
  }
  
  cartButton.addEventListener("click", openCart);
  cartClose.addEventListener("click", closeCart);
  cartOverlay.addEventListener("click", closeCart);
  
  /* BURGER MENU */
  
  burger.addEventListener("click", ()=>{
  
    burger.classList.toggle("active");
    navMenu.classList.toggle("active");
  
    const expanded = burger.classList.contains("active");
  
    burger.setAttribute("aria-expanded", expanded);
  });
  
  navLinks.forEach(link => {
    link.addEventListener("click", ()=>{
  
      navMenu.classList.remove("active");
      burger.classList.remove("active");
      burger.setAttribute("aria-expanded","false");
    });
  });
  
  
 /* ORDER FLOW */
 placeOrder.addEventListener("click", async ()=>{

  if(cartData.length === 0){
    alert("Your cart is empty.");
    return;
  }

  // get inputs
  const name = document.getElementById("customerName").value;
  const phone = document.getElementById("customerPhone").value;

  if(!name || !phone){
    alert("Please enter your name and phone number.");
    return;
  }

  try{
    const response = await fetch("api/save-order.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        name,
        phone,
        cart: cartData
      })
    });
  
    const result = await response.json();

    if(result.success){

      closeCart();

      orderModal.classList.add("show");
      orderModal.setAttribute("aria-hidden","false");

    }else{
      alert("Order failed.");
    }

  }catch(error){
    console.error(error);
    alert("Server error.");
  }

});

  function closeModal(){
    orderModal.classList.remove("show");
    orderModal.setAttribute("aria-hidden","true");
  
    cartData = [];
    updateCart();
  }
  
  orderOk.addEventListener("click", closeModal);
  
  document.querySelectorAll("[data-close]").forEach(el=>{
    el.addEventListener("click", closeModal);
  });
  
  /* CONTACT FORM */
  
  const contactForm = document.getElementById("contactForm");
  const contactNote = document.getElementById("contactNote");
  
  contactForm.addEventListener("submit",(e)=>{
    e.preventDefault();
  
    contactNote.textContent = "Message sent successfully ✨";
    contactForm.reset();
  
    setTimeout(()=>{
      contactNote.textContent = "";
    },3000);
  });
  
  /* YEAR */
  
  document.getElementById("year").textContent = new Date().getFullYear();
  
  /* INIT */
  
  renderMenu();
  updateCart();

  // =========================
// SCROLL REVEAL
// =========================
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add("active");
    }
  });
}, { threshold: 0.15 });

document.querySelectorAll(".reveal, .reveal-left, .reveal-right")
  .forEach(el => observer.observe(el));

    // =========================
//  LIVE REFRESH
// =========================
let trackInterval = null;

function startTracking() {
  const phone = document.getElementById("trackPhone").value;

  if (!phone) {
    alert("Enter phone number");
    return;
  }

  // stop old loop if exists
  if (trackInterval) clearInterval(trackInterval);

  fetchOrder(phone); // first load

  // LIVE refresh every 5 seconds
  trackInterval = setInterval(() => {
    fetchOrder(phone);
  }, 5000);
}

function fetchOrder(phone) {
  const box = document.getElementById("trackResult");

  box.innerHTML = "Loading order...";

  fetch(`api/get-order.php?phone=${phone}`)
    .then(res => res.json())
    .then(data => {

      if (!data.success) {
        box.innerHTML = "❌ No order found";
        return;
      }

      const order = data.order;

      let itemsHTML = "";
      data.items.forEach(item => {
        itemsHTML += `<li>${item.drink_name} × ${item.quantity}</li>`;
      });

      box.innerHTML = `
        <div class="status">
          <h3>Order #${order.id}</h3>
          <p><b>Name:</b> ${order.customer_name}</p>
          <p><b>Status:</b> ${order.status}</p>
          <p><b>Total:</b> $${order.total}</p>
          <p><b>Date:</b> ${order.created_at}</p>
          <h4>Items</h4>
          <ul>${itemsHTML}</ul>
        </div>
      `;
    })
    .catch(err => {
      box.innerHTML = "❌ Server error";
      console.log(err);
    });
}

function openTrackModal(){
  document.getElementById("trackModal").classList.add("show");
}

function closeTrack(){
  document.getElementById("trackModal").classList.remove("show");

  // stop live tracking when closed
  if(trackInterval){
    clearInterval(trackInterval);
    trackInterval = null;
  }
}