#include <iostream>
#include <map>
#include <string.h>
#include <string>
#include <sstream>
#include <iterator>
#include <algorithm>
#include <set>
#include <deque>
#include <boost/range/adaptor/reversed.hpp> 
#include "mysql_connection.h"
#include <cppconn/driver.h>
#include <cppconn/exception.h>
#include <cppconn/resultset.h>
#include <cppconn/statement.h>
#include <cppconn/prepared_statement.h>
#include <sys/wait.h>
#include <queue>
#include <thread>
#include <mutex>
#include <condition_variable>
#include <vector>
#include <memory>

//For Websockets

#include <boost/beast/core.hpp>
#include <boost/beast/websocket.hpp>
#include <boost/asio/ip/tcp.hpp>
#include <boost/asio/io_context.hpp>



// using namespace std;
// ID to Order Map required or Use referenced Order objects and clear memory before removing from lists...
// Not required if database updation done on-time (will have to be checked for latency)

class Order
{
public:
    int id, userid;
    std::string stock;
    char type;
    int price, price_fetched; //price_fetched not required for Buying Orders (introduce inherited Sell Order class)
    int quantity, init_quantity;
    Order(std::string request);
    std::mutex *sqlMutex; //storage issue to be handled (pooling can resolve this)
};

void ProcessOrder(Order);
void ProcessRequest(void);
void ProcessRequest(std::string request);

class Limit
{
public:
    int value;
    bool operator<(const Limit &other) const
    {
        return value < other.value;
    }
    Limit(int val);
    Limit(int val, Order o);

public: // Access Modifier to be changed
    std::deque<Order> list;
};

class OrderBooks
{
public:
    static std::unordered_map<std::string, OrderBooks> BookManager;
    // static std::unordered_map<int, Order> OrderTracker; //For deleting and editing orders
public:
    std::set<Limit> SellingTree;
    std::set<Limit> BuyingTree;
};

class MatchingEngine
{
public:
    static bool MatchWithSell(Order &order);
    static bool MatchWithBuy(Order &order);
};

class DatabaseHandler
{
public:
    static sql::Driver *driver;
    sql::Connection *con;
    sql::Statement *stmt;
    static void init();
    static std::string address, username, password;
    static void CreateOrder(Order);
    static void UpdateOrderS(int id, int qty_fulfill, int price_fetched, std::mutex *sqlMutex);
    static void UpdateOrderB(int id, int qty_fulfill, std::mutex *sqlMutex);
    DatabaseHandler();
    ~DatabaseHandler();
};

class WebSockets
{
public:
    // static std::vector<boost::beast::websocket::stream<boost::asio::ip::tcp::socket>*> openConnections;
    static std::vector<std::shared_ptr<boost::beast::websocket::stream<boost::asio::basic_stream_socket<boost::asio::ip::tcp>>>> openConnections;
    static void socketAcceptorThread();
    static void handleWebSocket(boost::asio::ip::tcp::socket&& socket);
    static void onMessage(boost::beast::websocket::stream<boost::asio::ip::tcp::socket>& ws, boost::beast::multi_buffer& buffer);
    static void broadcastThread(); 
};

extern std::queue<std::string> inputQueue;
extern std::mutex queueMutex, logicMutex, socketMutex;
extern std::condition_variable queueCV;
extern std::queue<std::string> outputQueue;
extern std::mutex outputMutex;
extern std::condition_variable outputCV;