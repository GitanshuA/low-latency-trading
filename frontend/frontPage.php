<?php
session_start();
register_shutdown_function('updateDatabase');

// Function to update the database
function updateDatabase() {
    $orders_update = $_COOKIE['orders_var'];
    $user_name = $_SESSION['username'];
    $conn = new mysqli('192.168.29.101', 'admin', 'admin', 'TradingDB');
    $sql = "UPDATE Users SET orders = $orders_update WHERE username=$user_name";
    $conn->query($sql);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading Page</title>
    <script src="https://kit.fontawesome.com/e402329cbc.js" crossorigin="anonymous"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles.css">
</head>

<body>
    <div class="overlay-rgb">
    <section id="navbar">
            <nav class="nav">
                <div class="icon">
                    <i class="fa-regular fa-circle-user fa-xl"></i>
                    <span><?php echo $_SESSION['username'];?> </span>
                </div>
                <div><a href="./frontPage.php">Home</a></div>
                <!-- <div><a href="">Current Bids</a></div> -->
                <div><a href="./orders.php">Orders</a></div>
            </nav>
        </section>
        <section id="home">
            <div class="display-stocks">
                <div class="stock aapl" onclick="toggleTrades(this)">
                    <img src="./images/aapl.png" alt="company-logo">
                    <div class="stock-options">
                        <div class="stock-info">
                            <h1>AAPL</h1>
                            <!-- <h2>Lowest Buying Price: <span class="bp-update"></span></h2>
                            <h2>Highest Selling Price: <span class="sp-update"></span></h2> -->
                        </div>
                        <div class="action-buttons" id="aapl">
                            <button type="button" class="buy" onclick="stockAction(this.parentNode.id, this.className)">Buy</button>
                            <button type="button" class="sell" onclick="stockAction(this.parentNode.id, this.className)">Sell</button>
                        </div>
                        <input type="text" name="" id="aapl-price" maxlength="7" class="price-input" placeholder="Price">
                        <input type="text" name="" id="aapl-qty" maxlength="7" class="qty-input" placeholder="Qty">
                    </div>
                </div>

                <div class="trades">
                    <div class="buying">
                        <h1>BUYING BIDS</h1>
                        <div class="price-qty-display">
                            <div class="price-display">
                                <h2>PRICE</h2>
                                <p></p>
                            </div>
                            <div class="qty-display">
                                <h2>QTY</h2>
                                <p></p>
                            </div>
                        </div>
                    </div>
                    <div class="selling">
                        <h1>SELLING BIDS</h1>
                        <div class="price-qty-display">
                            <div class="price-display">
                                <h2>PRICE</h2>
                                <p></p>
                            </div>
                            <div class="qty-display">
                                <h2>QTY</h2>
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stock googl" onclick="toggleTrades(this)">
                    <img src="./images/googl.png" alt="company-logo">
                    <div class="stock-options">
                        <div class="stock-info">
                            <h1>GOOGL</h1>
                            <!-- <h2>Lowest Buying Price: <span class="bp-update"></span></h2>
                            <h2>Highest Selling Price: <span class="sp-update"></span></h2> -->
                        </div>
                        <div class="action-buttons" id="googl">
                            <button type="button" class="buy" onclick="stockAction(this.parentNode.id, this.className)">Buy</button>
                            <button type="button" class="sell" onclick="stockAction(this.parentNode.id, this.className)">Sell</button>
                        </div>
                        <input type="text" name="" id="googl-price" maxlength="7" class="price-input" placeholder="Price">
                        <input type="text" name="" id="googl-qty" maxlength="7" class="qty-input" placeholder="Qty">
                    </div>
                </div>


                <div class="trades">
                    <div class="buying">
                        <h1>BUYING BIDS</h1>
                        <div class="price-qty-display">
                            <div class="price-display">
                                <h2>PRICE</h2>
                                <p></p>
                            </div>
                            <div class="qty-display">
                                <h2>QTY</h2>
                                <p></p>
                            </div>
                        </div>
                    </div>
                    <div class="selling">
                        <h1>SELLING BIDS</h1>
                        <div class="price-qty-display">
                            <div class="price-display">
                                <h2>PRICE</h2>
                                <p></p>
                            </div>
                            <div class="qty-display">
                                <h2>QTY</h2>
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stock msft" onclick="toggleTrades(this)">
                    <img src="./images/msft.png" alt="company-logo">
                    <div class="stock-options">
                        <div class="stock-info">
                            <h1>MSFT</h1>
                            <!-- <h2>Lowest Buying Price: <span class="bp-update"></span></h2>
                            <h2>Highest Selling Price: <span class="sp-update"></span></h2> -->
                        </div>
                        <div class="action-buttons" id="msft">
                            <button type="button" class="buy" onclick="stockAction(this.parentNode.id, this.className)">Buy</button>
                            <button type="button" class="sell" onclick="stockAction(this.parentNode.id, this.className)">Sell</button>
                        </div>
                        <input type="text" name="" id="msft-price" maxlength="7" class="price-input" placeholder="Price">
                        <input type="text" name="" id="msft-qty" maxlength="7" class="qty-input" placeholder="Qty">
                    </div>
                </div>


                <div class="trades">
                    <div class="buying">
                        <h1>BUYING BIDS</h1>
                        <div class="price-qty-display">
                            <div class="price-display">
                                <h2>PRICE</h2>
                                <p></p>
                            </div>
                            <div class="qty-display">
                                <h2>QTY</h2>
                                <p></p>
                            </div>
                        </div>
                    </div>
                    <div class="selling">
                        <h1>SELLING BIDS</h1>
                        <div class="price-qty-display">
                            <div class="price-display">
                                <h2>PRICE</h2>
                                <p></p>
                            </div>
                            <div class="qty-display">
                                <h2>QTY</h2>
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stock tsla" onclick="toggleTrades(this)">
                    <img src="./images/tsla.png" alt="company-logo">
                    <div class="stock-options">
                        <div class="stock-info">
                            <h1>TSLA</h1>
                            <!-- <h2>Lowest Buying Price: <span class="bp-update"></span></h2>
                            <h2>Highest Selling Price: <span class="sp-update"></span></h2> -->
                        </div>
                        <div class="action-buttons" id="tsla">
                            <button type="button" class="buy" onclick="stockAction(this.parentNode.id, this.className)">Buy</button>
                            <button type="button" class="sell" onclick="stockAction(this.parentNode.id, this.className)">Sell</button>
                        </div>
                        <input type="text" name="" id="tsla-price" maxlength="7" class="price-input" placeholder="Price">
                        <input type="text" name="" id="tsla-qty" maxlength="7" class="qty-input" placeholder="Qty">
                    </div>
                </div>


                <div class="trades">
                    <div class="buying">
                        <h1>BUYING BIDS</h1>
                        <div class="price-qty-display">
                            <div class="price-display">
                                <h2>PRICE</h2>
                                <p></p>
                            </div>
                            <div class="qty-display">
                                <h2>QTY</h2>
                                <p></p>
                            </div>
                        </div>
                    </div>
                    <div class="selling">
                        <h1>SELLING BIDS</h1>
                        <div class="price-qty-display">
                            <div class="price-display">
                                <h2>PRICE</h2>
                                <p></p>
                            </div>
                            <div class="qty-display">
                                <h2>QTY</h2>
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stock amzn" onclick="toggleTrades(this)">
                    <img src="./images/amzn.png" alt="company-logo">
                    <div class="stock-options">
                        <div class="stock-info">
                            <h1>AMZN</h1>
                            <!-- <h2>Lowest Buying Price: <span class="bp-update"></span></h2>
                            <h2>Highest Selling Price: <span class="sp-update"></span></h2> -->
                        </div>
                        <div class="action-buttons" id="amzn">
                            <button type="button" class="buy" onclick="stockAction(this.parentNode.id, this.className)">Buy</button>
                            <button type="button" class="sell" onclick="stockAction(this.parentNode.id, this.className)">Sell</button>
                        </div>
                        <input type="text" name="" id="amzn-price" maxlength="7" class="price-input" placeholder="Price">
                        <input type="text" name="" id="amzn-qty" maxlength="7" class="qty-input" placeholder="Qty">
                    </div>
                </div>


                <div class="trades">
                    <div class="buying">
                        <h1>BUYING BIDS</h1>
                        <div class="price-qty-display">
                            <div class="price-display">
                                <h2>PRICE</h2>
                                <p></p>
                            </div>
                            <div class="qty-display">
                                <h2>QTY</h2>
                                <p></p>
                            </div>
                        </div>
                    </div>
                    <div class="selling">
                        <h1>SELLING BIDS</h1>
                        <div class="price-qty-display">
                            <div class="price-display">
                                <h2>PRICE</h2>
                                <p></p>
                            </div>
                            <div class="qty-display">
                                <h2>QTY</h2>
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stock meta" onclick="toggleTrades(this)">
                    <img src="./images/meta.png" alt="company-logo">
                    <div class="stock-options">
                        <div class="stock-info">
                            <h1>META</h1>
                            <!-- <h2>Lowest Buying Price: <span class="bp-update"></span></h2>
                            <h2>Highest Selling Price: <span class="sp-update"></span></h2> -->
                        </div>
                        <div class="action-buttons" id="meta">
                            <button type="button" class="buy" onclick="stockAction(this.parentNode.id, this.className)">Buy</button>
                            <button type="button" class="sell" onclick="stockAction(this.parentNode.id, this.className)">Sell</button>
                        </div>
                        <input type="text" name="" id="meta-price" maxlength="7" class="price-input" placeholder="Price">
                        <input type="text" name="" id="meta-qty" maxlength="7" class="qty-input" placeholder="Qty">
                    </div>
                </div>
                <div class="trades">
                    <div class="buying">
                        <h1>BUYING BIDS</h1>
                        <div class="price-qty-display">
                            <div class="price-display">
                                <h2>PRICE</h2>
                                <p></p>
                            </div>
                            <div class="qty-display">
                                <h2>QTY</h2>
                                <p></p>
                            </div>
                        </div>
                    </div>
                    <div class="selling">
                        <h1>SELLING BIDS</h1>
                        <div class="price-qty-display">
                            <div class="price-display">
                                <h2>PRICE</h2>
                                <p></p>
                            </div>
                            <div class="qty-display">
                                <h2>QTY</h2>
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="pending">

        </section>
        <section id="completed">

        </section>
    </div>
    <div class="order-placed-message">
        <p>Order Placed!</p>
        <div class="progress-bar">
            
        </div>
    </div>
    
    <script>
        function toggleTrades(obj) {
            var tradesDiv = obj.nextElementSibling;
            tradesDiv.style.display = tradesDiv.style.display === 'none' ? 'flex' : 'none';
            obj.style.marginBottom = tradesDiv.style.display === 'none' ? "10px" : "0px";
            obj.style.marginBottom = tradesDiv.style.display === 'none' ? "10px" : "0px";
            obj.style.borderBottomRightRadius = tradesDiv.style.display === 'none' ? "10px" : "0px";
            obj.style.borderBottomLeftRadius = tradesDiv.style.display === 'none' ? "10px" : "0px";

        }
    </script>
    <script>
        var user_id = <?php echo json_encode($_SESSION['user_id']); ?>;
    </script>
    <script>
        var orderID, stockName, qty, price, returnMessage, displayMessage;
        var isBuying = true;

        // fetch the userID from the database... to be made
        function showProgressBar() {
            msgBox = document.querySelector('.order-placed-message');
            msgBox.style.display = "inline-block";
            progressBar = document.querySelector('.progress-bar');
            progressBar.style.width = "0%";
            setTimeout(function () {
                progressBar.style.width = '100%';
            }, 5);

            setTimeout(function () {
                msgBox.style.display = 'none';
            }, 2500);

        }

        const socket = new WebSocket('ws://192.168.29.101:9003');

        socket.onopen = () => {
            console.log('Connected to WebSocket server-!-');
        };

        socket.onmessage = (event) => {
            displayMessage = event.data;
            console.log(typeof (displayMessage));
            console.log("Display:  " + displayMessage);
            if (displayMessage.charAt(0) == 'O') {
                showProgressBar();
            } else {
                // st,B,pr,qty
                arr = displayMessage.split(" ");
                element = document.querySelector("." + arr[0].toLowerCase()).nextElementSibling;
                if (arr[1] == 'B') {
                    var tradeElement = element.querySelector('.buying');
                } else {
                    var tradeElement = element.querySelector('.selling');
                }
                var priceElement = tradeElement.querySelector('.price-display p');
                var qtyElement = tradeElement.querySelector('.qty-display p');
                priceElement.innerHTML = (arr[2]+"<br>") + priceElement.innerHTML;
                qtyElement.innerHTML = (arr[3]+"<br>") + qtyElement.innerHTML;
            }
            // console.log('Received from server', displayMessage);
        };

        socket.onerror = (error) => {
            displayMessage = "Unforeseen error";
        };

        socket.onclose = (event) => {
            console.log('WebSocket connection closed:', event);
        };
        <?php
            if ($_COOKIE['orders_var'] != NULL) {
                $_SESSION['orders'] = $_COOKIE['orders_var'];
            }
            // $conn = new mysqli("192.168.29.101", "admin", "admin", "TradingDB");
            // $sql = "SELECT orders FROM Users WHERE username = '{$_SESSION['username']}'";
            // $result = $conn->query($sql);
            // $row_res = $result->fetch_assoc();
            // $order_number = $row_res['orders'] + 1; // Increment the order number
            // $sql_update = "UPDATE Users SET orders = $order_number WHERE username = '{$_SESSION['username']}'";
            // $result_update = $conn->query($sql_update);
        ?>;

        var orders = <?php echo json_encode($_SESSION['orders']); ?>;
        function stockAction(stockID, action) {

        orders = (parseInt(orders, 10)+1).toString();
        document.cookie = "orders_var = " + orders;
    orderID = user_id + orders;
    console.log(action);
    console.log("order: " + orders);
    if (action == "buy") {
        returnMessage = buyStock(stockID);
    } else {
        returnMessage = sellStock(stockID);
    }
    console.log(returnMessage);

    if (returnMessage == "-1") {
        displayMessage = "Enter Valid Inputs";
    } else {
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
                returnMessage = orderID + " " + user_id + " BUY " + stockName + " " + price + " " + qty;
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
                returnMessage = orderID + " " + user_id + " SELL " + stockName + " " + price + " " + qty;
            }

            return returnMessage;
        }
    </script>
</body>

</html>
