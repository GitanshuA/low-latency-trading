#include "headers.hpp"

Limit::Limit(int val)
    {
        value = val;
    }

Limit::Limit(int val, Order o)
    {
        value = val;
        list.emplace_back(o);
    }