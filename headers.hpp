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
// using namespace std;
// ID to Order Map required or Use referenced Order objects and clear memory before removing from lists...
// Not required if database updation done on-time (will have to be checked for latency)

class Order
{
public: // Access Modifier to be changed
    std::string id, stock;
    char type;
    int price;
    int quantity;

public:
    Order(std::string request);
};

void ProcessOrder(Order);
void ProcessRequest(void);

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
    static std::map<std::string, OrderBooks> BookManager;

public: // Access Modifier to be changed
    std::set<Limit> SellingTree;
    std::set<Limit> BuyingTree;
};

// Order fulfillment should happen parallely while not constraining new orders
class MatchingEngine
{
public:
    static bool MatchWithSell(Order &order);
    static bool MatchWithBuy(Order &order);
};