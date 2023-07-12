#include "headers.hpp"

std::string DatabaseHandler::address, DatabaseHandler::username, DatabaseHandler::password;
sql::Driver *DatabaseHandler::driver;

void DatabaseHandler::init()
{
    std::cout << "Enter Server IP address: ";
    std::cin >> address;
    std::cout << "Enter Username: ";
    std::cin >> username;
    std::cout << "Enter Password: ";
    std::cin >> password;
    driver = get_driver_instance();
    sql::Connection *con = driver->connect("tcp://" + address, username, password);
    sql::Statement *stmt = con->createStatement();
    stmt->execute("CREATE DATABASE IF NOT EXISTS TradingDB;");
    stmt->execute("USE TradingDB");
    stmt->execute("CREATE TABLE IF NOT EXISTS Sell_Orders(order_id INT PRIMARY KEY, user_id INT, ticker_id VARCHAR(8), qty INT, pub_price INT, qty_sold INT DEFAULT 0, price_fetched INT DEFAULT 0)");
    stmt->execute("CREATE TABLE IF NOT EXISTS Buy_Orders(order_id INT PRIMARY KEY, user_id INT, ticker_id VARCHAR(8), qty INT, pub_price INT, qty_sold INT DEFAULT 0)");
    delete stmt;
    con->close();
    delete con;
}

void DatabaseHandler::CreateOrder(Order o)
{
    std::lock_guard<std::mutex> sql_lock(*(o.sqlMutex));
    std::string message = o.stock+" "+o.type+" "+std::to_string(o.price)+" "+std::to_string(o.quantity);
    {
        std::lock_guard<std::mutex> lock(outputMutex);
        outputQueue.push(message);
    }
    queueCV.notify_one();
    DatabaseHandler dh;
    if (o.type == 'S')
        dh.stmt->execute("INSERT INTO Sell_Orders(order_id, user_id, ticker_id, qty, pub_price) VALUES(" + std::to_string(o.id) + ", " + std::to_string(o.userid) + ", \"" + o.stock + "\", " + std::to_string(o.quantity) + ", " + std::to_string(o.price) + ")");
    else
        dh.stmt->execute("INSERT INTO Buy_Orders(order_id, user_id, ticker_id, qty, pub_price) VALUES(" + std::to_string(o.id) + ", " + std::to_string(o.userid) + ", \"" + o.stock + "\", " + std::to_string(o.quantity) + ", " + std::to_string(o.price) + ")");
}

DatabaseHandler::DatabaseHandler()
{
    con = DatabaseHandler::driver->connect("tcp://" + address, username, password);
    con->setSchema("TradingDB");
    stmt = con->createStatement();
}

DatabaseHandler::~DatabaseHandler()
{
    delete stmt;
    con->close();
    delete con;
}