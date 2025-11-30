@extends('frontend.layouts.main')

@section('main-content')
<style>
  .checkout-container {
    background-color: #fff;
    padding: 20px;
    font-size: 16px;
  }

  .checkout-container .order-summary,
  .checkout-container .payment-section {
    border: 1px solid #ddd;
    padding: 20px;
    margin-bottom: 20px;
    background: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
  }

  .checkout-container h4 {
    margin-bottom: 20px;
    font-weight: bold;
    font-size: 20px;
  }

  .checkout-container .form-control,
  .checkout-container .form-check-input {
    border-radius: 8px;
  }

  .checkout-container .form-check-label {
    font-size: 15px;
  }

  .checkout-container .payment-methods {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
  }

 .checkout-container .payment-box {
  flex: 1 1 45%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 15px;
  border: 2px solid #ccc;
  border-radius: 10px;
  background-color: #fafafa;
  cursor: pointer;
  transition: background-color 0.4s ease, color 0.4s ease, border-color 0.3s;
  color: #000;
}

.checkout-container .payment-box:hover {
  background-color: #0A0F2C;
  color: #fff;
}
  .checkout-container .payment-box img {
    height: 30px;
    margin-right: 10px;
  }
/* Selected payment option */
.checkout-container .payment-box.selected {
  background-color: #0A0F2C;
  color: #fff;
  border-color: #0A0F2C;
}
.checkout-container .payment-box.selected img {
  filter: brightness(0) invert(1); /* Makes logos visible on dark bg */
}
  .checkout-container .place-order-btn {
    width: 100%;
    padding: 15px;
    background-color:#0A0F2C;
    color: white;
    font-weight: bold;
    font-size: 18px;
    border: none;
    margin-top: 20px;
    transition: background-color 0.3s ease;
  }

  .checkout-container .place-order-btn:hover {
    color:#0A0F2C;
    background:goldenrod;
  }

  /* Fonepay info box */
  .checkout-container .fonepay-info {
    margin-top: 20px;
    padding: 15px;
    border: 1px dashed #0A0F2C;
    border-radius: 10px;
    text-align: center;
    display: none;
  }
  .checkout-container .fonepay-info img {
    max-width: 200px;
    margin-bottom: 10px;
  }

  @media (max-width: 768px) {
    .checkout-container .payment-box {
      flex: 1 1 100%;
    }
  }
</style>
@extends('frontend.layouts.main')

@section('main-content')
<style>
/* (your same CSS — unchanged) */
</style>

<div class="container checkout-container">
  <div class="row">

    <!-- Left Column -->
    <div class="col-lg-6">
      <div class="order-summary">
        <h4>Billing & Shipping Address</h4>
        <form>
          <div class="form-group mb-3">
            <label>Full Name *</label>
            <input type="text" class="form-control" name="full_name" required>
          </div>

          <div class="form-group mb-3">
            <label>Phone *</label>
            <input type="text" name="phone" class="form-control" required>
          </div>

          <div class="form-group mb-3">
            <label>Enter your Address/Landmark here *</label>
            <input type="text" name="address" class="form-control" required>
          </div>

          <div class="form-group mb-3">
            <label>Delivery *</label>
            <div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="delivery" value="inside" checked>
                <label class="form-check-label">Inside Valley: Rs. 100</label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="radio" name="delivery" value="outside">
                <label class="form-check-label">Outside Valley: <i>(May vary)</i> Rs. 200</label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="radio" name="delivery" value="pickup">
                <label class="form-check-label">Store Pickup: Free</label>
              </div>
            </div>
          </div>

          <div class="form-group mt-3" style="height:200px;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18..."
              style="width:100%; height:100%; border:0;"
              allowfullscreen loading="lazy"></iframe>
          </div>

        </form>
      </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-6">

      <div class="payment-section">
        <h4>Choose Payment Options</h4>

        <div class="payment-methods">

          <div class="payment-box" data-method="fonepay">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRVm2cDdqOxx_Y_7HzvD6sh_QYx3Nrp-xi07Q&s">
            <span>Fonepay</span>
          </div>

          <div class="payment-box" data-method="connectips">
            <img src="https://via.placeholder.com/80x40?text=ConnectIPS">
            <span>Connect IPS</span>
          </div>

          <div class="payment-box" data-method="cod">
            <span>Cash on Delivery</span>
          </div>

        </div>

        <!-- Fonepay Info -->
        <div class="fonepay-info" id="fonepayInfo">
          <img src="https://via.placeholder.com/200x200?text=Fonepay+QR">
          <p>Please put your <b>Name</b> & <b>Phone Number</b> in remarks.<br>
            Send screenshot on WhatsApp: <b>+977 9849433139</b>
          </p>
        </div>

        <!-- ConnectIPS Info -->
        <div class="fonepay-info" id="connectInfo">
          <img src="https://via.placeholder.com/200x200?text=ConnectIPS+QR">
          <p>Please put your <b>Name</b> & <b>Phone Number</b> in remarks.<br>
            Send screenshot on WhatsApp: <b>+977 9849433139</b>
          </p>
        </div>

        <div class="form-check mt-4">
          <input class="form-check-input" type="checkbox" id="termsCheck">
          <label class="form-check-label">I agree to the terms & conditions *</label>
        </div>

        <button class="place-order-btn">Place Order</button>
      </div>

      <!-- Mock Order Summary -->
      <div class="order-summary mt-3">
        <h4>Your Order</h4>

        <p><strong>Product:</strong> Fresh Apples (x2) - Rs. 300.00</p>
        <p><strong>Product:</strong> Organic Potatoes (x1) - Rs. 120.00</p>

        <p><strong>Subtotal:</strong> Rs. 420.00</p>
        <p><strong>Delivery Charge:</strong> Rs. 100.00</p>

        <hr>
        <p><strong>Total:</strong> Rs. 520.00 <small>(Tax inclusive)</small></p>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

  const paymentBoxes = document.querySelectorAll(".payment-box");
  const fonepayInfo = document.getElementById("fonepayInfo");
  const connectInfo = document.getElementById("connectInfo");
  let selectedPaymentMethod = null;

  // Handle payment selection
  paymentBoxes.forEach(box => {
    box.addEventListener("click", () => {
      paymentBoxes.forEach(b => b.classList.remove("selected"));
      box.classList.add("selected");
      selectedPaymentMethod = box.dataset.method;

      fonepayInfo.style.display = selectedPaymentMethod === "fonepay" ? "block" : "none";
      connectInfo.style.display = selectedPaymentMethod === "connectips" ? "block" : "none";
    });
  });

  // Place order (Frontend only)
  document.querySelector(".place-order-btn").addEventListener("click", () => {

    const name = document.querySelector('input[name="full_name"]').value.trim();
    const phone = document.querySelector('input[name="phone"]').value.trim();
    const address = document.querySelector('input[name="address"]').value.trim();

    // Basic validations
    if (!name || !phone || !address) {
      alert("Please fill all required fields.");
      return;
    }
    if (!selectedPaymentMethod) {
      alert("Please select a payment method.");
      return;
    }
    if (!document.getElementById("termsCheck").checked) {
      alert("Please agree to terms and conditions.");
      return;
    }

    // Mock success
    alert("Order placed successfully! (No backend used)");
  });

});
</script>


@endsection
