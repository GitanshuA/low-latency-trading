<?php session_start(); 
$_SESSION['orders'] = $_COOKIE['orders_var'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <script src="https://kit.fontawesome.com/e402329cbc.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./orders.css">
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
        <section id="orders">
            <?php
            $conn = new mysqli('192.168.29.101', 'admin', 'admin', 'TradingDB');
            $user_id = $_SESSION['user_id'];

            $sellSql = "SELECT ticker_id, qty_fulfill, price_fetched, order_time FROM Sell_Orders WHERE NOT qty_fulfill = qty AND user_id=$user_id";
            $sellResult = $conn->query($sellSql);

            $buySql = "SELECT ticker_id, qty_fulfill, order_time, pub_price FROM Buy_Orders WHERE NOT qty_fulfill = qty AND user_id=$user_id";
            $buyResult = $conn->query($buySql);

            $combinedResults = array();
            while ($sellRow = $sellResult->fetch_assoc()) {
                $sellRow['type'] = '<font color="red">Sell</font>';
                $combinedResults[] = $sellRow;
            }
            while ($buyRow = $buyResult->fetch_assoc()) {
                $buyRow['price_fetched'] = $buyRow['qty_fulfill'] * $buyRow['pub_price'];
                unset($buyRow['pub_price']);
                $buyRow['type'] = '<font color="green">Buy</font>';
                $combinedResults[] = $buyRow;
            }

            usort($combinedResults, function ($a, $b) {
                return strtotime($b['order_time']) - strtotime($a['order_time']);
            });

            echo '<div class="order-list">';
            echo '<div class="res" style="margin-bottom:30px;font-size:27px;">';
            echo '<div class="stock-name">Stock Name</div>';
            echo '<div class="stock-name">Quantity Fulfilled</div>';
            echo '<div class="stock-name">Price Fetched</div>';
            echo '<div class="stock-name">Buy/Sell</div>';
            echo '<div class="stock-name">Date and Time</div>';
            echo '</div>';
            foreach ($combinedResults as $row) {
                echo '<div class="res">';
                echo '<div class="stock-name">' . strtoupper($row['ticker_id']) . '</div>';
                echo '<div class="qty-fulfill">' . $row['qty_fulfill'] . '</div>';
                if (isset($row['price_fetched'])) {
                    echo '<div class="price-fetched">' . $row['price_fetched'] . '</div>';
                    
                    echo '<div class="buy-or-sell">' . $row['type'] . '</div>';
                } else {
                    echo '<div class="price-fetched">NA</div>';
                    echo '<div class="buy-or-sell">' . $row['type'] . '</div>';
                }
                echo '<div class="order-time">' . date('Y-m-d H:i:s', strtotime($row['order_time'])) . '</div><br><br><br>';
                echo '</div>';
            }
            echo '</div>';
            ?>
        </section>
    </div>
</body>

</html>
