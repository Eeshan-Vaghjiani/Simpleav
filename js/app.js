const header = document.querySelector("header");
window.addEventListener("scroll", function (){
  header.classList.toggle("sticky" , window.scrollY>150);
});

let menu = document.querySelector('#menu-icon');
let navlinks = document.querySelector('.navlinks' )
menu.onclick = () =>{
  menu.classList.toggle('bx-x');
  navlinks.classList.toggle('open');

}
window.onscroll = ()=>{
  menu.classList.remove('bx-x');
  navlinks.classList.remove('open');
}
document.addEventListener('DOMContentLoaded', function() {
  const paymentForm = document.getElementById('payment-form');

  paymentForm.addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Get the input values
    const cardName = document.getElementById('card-name').value.trim();
    const cardNumber = document.getElementById('card-number').value.trim();
    const expiry = document.getElementById('expiry').value.trim();
    const cvv = document.getElementById('cvv').value.trim();

    // Basic validation
    if (!cardName || !cardNumber || !expiry || !cvv) {
      alert('Please fill in all fields.');
      return;
    }

    // Simulate a payment processing (you can replace this with an actual API call)
    setTimeout(() => {
      // Simulate success response
      alert('Payment successful! Thank you for your purchase, ' + cardName + '!');
      // Optionally, you can reset the form
      paymentForm.reset();
    }, 2000); // Simulating a delay of 2 seconds for processing
  });
});
