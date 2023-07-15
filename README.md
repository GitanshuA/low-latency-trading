<a name="readme-top"></a>

<!-- PROJECT LOGO -->
<br />
<div align="center">

<h3 align="center">low-latency-trading</h3>

  <p align="center">
    An attempt at implementing a basic low-latency trading system using C++ and WebSocket
    <br />
    <a href="https://github.com/GitanshuA/low-latency-trading"><strong>Explore the code »</strong></a>
    <br />
  </p>
</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#contributors">Contributors</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

This project tries to implement a basic low-latency trading system using C++ and WebSocket.

### Basic Workflow
1. When a client opens the webpage, a WebSocket connection request is sent to the back-end server. The server accepts the request, and the WebSocket     connection gets established. 
2. When the client tries to place an order, a request is sent through websocket to the server. The server receives the request and broadcasts it to     all connected clients.
3. The backend tries to match the order against existing orders.
4. Once matching is tried, depending on the fulfillment status of the order, the order may be added to the program memory as a pending order. Then the status of the orders gets updated in the database.  

### Backend Architecture
The Backend architecture can be broadly divided into 3 parts: (i) Trade Processing unit, (ii) WebSocket connection handling unit and (iii) Database handling unit
When the server is started, a connection is established with the database and the appropriate tables are created in the database (if they don't already exist). After that a separate thread is created to handle the WebSocket connection requests, and also a thread is created to broadcast incoming trade requests to all the clients. Then the main loop for processing the trade requests begins.

#### WebSocket handling unit
* When a new user tries to connect, first the acceptor thread accepts the WebSocket connection and then starts a new thread to handle the new WebSocket client. This thread keeps on checking continuosly for any trade request from client.
* Also, the connection gets pushed in a global connections vector which is used to broadcast messages to the connected clients
* When a trade request is received from any client, the respective thread pushes the request into a thread-safe queue and notifies the main Trade Processing unit about the new request.
* When a trade request is received from any client the request is pushed into a thread-safe output queue, which is read by the broadcaster thread and trade requests are broadcasted to all clients.

#### Trade processing unit
* The trade processing unit consists of two binary search trees (C++ std::set used for implementation), the Buying Tree and Selling Tree, which respectively store the buying and selling trade orders in deques, arranged by limit prices.
* When an order is received, it is first checked whether the limit price of the order already exists in the tree. If it does, then the order is directly added to the corresponding deque(because if there are pending orders with the same price then there is no way that this order may get fulfilled first).
* If the limit prices doesn't exist already, then if the order is a selling order, the order is tried to be matched with the highest buying order and so on and if it's a buying order, it is tried to be matched with the lowest selling order and so on. The orders within the same limit are fulfilled according to time of arrival.
* If the order is not fulfilled completely, it is added to the appropriate limit deque.
* The matched and fulfilled orders are removed from the respective deques

#### Database handling unit
* While processing the order, at appropriate places database update threads are created which establish connection with the database and update the order status.
* All orders have their uniquely assigned mutexes which are locked while database update to ensure that any particular record gets updated in the correct order.


### Built With

* [![CPP][cpp_img]][cpp_url]
* [![MySQL][mysql_img]][mysql_url]
* [![JS][js_img]][js_url]
* WebSocket API

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

### Prerequisites

In order to run the program, the following dependencies need to be installed:-
* XAMPP or any other web server stack package with support for a MySQL-based database. XAMPP can be downloaded from [here](https://www.apachefriends.org/download.html).
* MySQL Connector/C++ and Boost libraries for compiling the code.
  ```sh
  sudo apt install libmysqlcppconn-dev
  sudo apt install libboost-all-dev
  ```

### Installation

1. Set up a user account on the database server with general usage permissions along with permissions for creating databases and tables.
2. Clone the repo
   ```sh
   git clone https://github.com/GitanshuA/low-latency-trading.git
   ```
3. Copy the `frontend` folder into the `/opt/lampp/htdocs` directory.
4. Compile the C++ source files using the makefile
   ```sh
   make
   ```
   This will compile the source files and create an executable file `backend`.
   Alternatively, On Ubuntu you can also directly use the executable `backend` provided in the repository without having to compile the code
<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- USAGE EXAMPLES -->
## Usage

1. Start the backend server
   ```sh
   ./backend
   Enter Server IP address: 192.168.29.30
   Enter Username: admin
   Enter Password: admin
   ```
   Enter the Server IP address and credentials for the Database Server when prompted
   Now the backend server will start running and listening for WebSocket connections
2. Now on your web browser open `localhost/frontend/index.php` or `[SERVER_IP_ADDRESS]/frontend/index.php`. This will take you to the client webpage where you can see 
   the options to trade various stocks
3. By clicking on a stock, you can see the trade requests for that stock published by other users. The trade requests from users are broadcasted to     other users at live-time using WebSocket.
4. In the orders section, the details of pending as well as completed orders are displayed.  

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- CONTACT -->
## Contributors

Gitanshu Arora ([GitanshuA](https://github.com/GitanshuA))

Advait Jain ([adv8j](https://github.com/adv8j))

Project Link: [https://github.com/GitanshuA/low-latency-trading](https://github.com/GitanshuA/low-latency-trading)

<p align="right">(<a href="#readme-top">back to top</a>)</p>

[cpp_img]: https://img.shields.io/badge/C++-00599C?style=flat-square&logo=C%2B%2B&logoColor=white
[cpp_url]: https://isocpp.org/
[mysql_img]: https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white
[mysql_url]: https://www.mysql.com/
[js_img]: https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript&logoColor=%23F7DF1E
[js_url]: https://developer.mozilla.org/en/JavaScript
