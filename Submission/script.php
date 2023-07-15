
<script>
var  orderID, stockName, qty, price, returnMessage, displayMessage;
var isBuying = true; 
// fetch the userID from the database... to be made

// Create a new WebSocket connection
const socket = new WebSocket('ws://192.168.29.101:9003');

// Event handler for when the connection is established
socket.onopen = () => {
  console.log('Connected to WebSocket server-!-');
  
};

// Event handler for receiving messages from the server
socket.onmessage = (event) => {
  // Retrieve the received message
  displayMessage = event.data;
  console.log(typeof(displayMessage));
  console.log("Display:  "+displayMessage);
  if (displayMessage.charAt(0)=='O') {
    showProgressBar();
  }
  else
  {
    // st,B,pr,qty
    arr = displayMessage.split(" ");
    element = document.querySelector("."+arr[0].toLowerCase()).nextElementSibling;
    if (arr[1]=='B') {
      
      var tradeElement = element.querySelector('buying');
    }
    else{
      var tradeElement = element.querySelector('selling');

    }
    var priceElement = tradeElement.querySelector('.price-display p');
    var qtyElement = tradeElement.querySelector('.qty-display p');
    priceElement.innerHTML = arr[2]+priceElement.innerHTML;
    qtyElement.innerHTML = arr[3]+qtyElement.innerHTML;
  }
  // Process the received message
  // console.log('Received from server', displayMessage);
};

// Event handler for errors
socket.onerror = (error) => {
  displayMessage = "Unforeseen error"
};

// Event handler for when the connection is closed
socket.onclose = (event) => {
  console.log('WebSocket connection closed:', event);
};

function stockAction(stockID, action) {
  $conn = new mysqli('192.168.29.101', 'admin', 'admin', 'TradingDB');

  
  var orders = <?php echo json_encode($_SESSION['orders']); ?>;
  orderID = user_id + orders*100000;// ensure it is a 5 dig. number; update in db; deal with orders in int only;order msg timeout
  console.log(action)
  if (action == "buy") {
    returnMessage = buyStock(stockID);
  } else {
    returnMessage = sellStock(stockID);
  }
  console.log(returnMessage);
  if (returnMessage == "-1") {
    displayMessage = "Enter Valid Inputs";
  } else {
    // Send returnMessage to the server
    <?php $_SESSION['orders']=$_SESSION['orders']+1; ?>
    $sql = "UPDATE Users SET orders= $_SESSION['orders'] WHERE username= $_SESSION['username']";
    $result = $conn->query($sql);
    socket.send(returnMessage);
  }
}

function buyStock(id) {
  stockName = id;
  let qtyName = stockName + "-qty";
  let priceName = stockName + "-price";
  qty = document.getElementById(qtyName);
  price = document.getElementById(priceName);
  qty = parseFloat(qty.value);
  price = parseFloat(price.value);
  
  if (isNaN(qty) || qty <= 0 || isNaN(price) || price <= 0) {
    returnMessage = "-1";
  } else {
    returnMessage = orderID + " " + user_id + " BUY "+stockName+" " + price + " " + qty;
  }
  
  return returnMessage;
}

function sellStock(id) {
  stockName = id;
  let qtyName = stockName + "-qty";
  let priceName = stockName + "-price";
  qty = document.getElementById(qtyName);
  price = document.getElementById(priceName);
  qty = parseFloat(qty.value);
  price = parseFloat(price.value);
  
  if (isNaN(qty) || qty <= 0 || isNaN(price) || price <= 0) {
    returnMessage = "-1";
  } else {
    returnMessage = orderID + " " + user_id + " SELL "+stockName+" " + price + " " + qty;
  }
  
  return returnMessage;
}
function toggleTrades() {
        var tradesDiv = document.querySelector('.trades');
        tradesDiv.style.display = tradesDiv.style.display === 'none' ? 'flex' : 'none';
    }
</script>