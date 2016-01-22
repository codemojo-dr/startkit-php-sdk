Welcome to the CodeMojo SDK (Startup Development Toolkit)

Using the SDK is straight forward and simple. The SDK consist of three main services

1. [Authentication Service](wiki/Authentication-Service)
2. [Wallet Service](wiki/Wallet-Service)
3. [Loyalty Service](wiki/Loyalty-Service)

###Authentication Service###
Authentication service is the basic requirement for all the other services. The constructor of this service takes in 2 parameters, `client id` and `client secret`

**Dependency** None

**Usage**

```php
use DRewards\Client\Services\AuthenticationService;

$authService = new AuthenticationService(CLIENT_ID, CLIENT_SECRET);
```
###Wallet Service###
You can use wallet service to work on a raw level transactions of crediting and debiting to user accounts

**Dependency** [Authentication Service](wiki/Authentication-Service)

**Usage**

```php
use DRewards\Client\Services\WalletService;

$walletService = new WalletService($authService);
```

###Loyalty Service###
Loyalty service adds logical layer to the wallet service which makes sense to manipulate rewards based on different criteria

**Dependency** [Authentication Service](wiki/Authentication-Service), [Wallet Service](wiki/Wallet-Service)

**Usage**

```php
use DRewards\Client\Services\LoyaltyService;

$loyaltyService = new LoyaltyService($authService);
```

### Example usage of Loyalty ###
Once you have enabled and configured the Loyalty module from your CodeMojo dashboard, you can use it as simple as shown below

```php    
require_once '../sdk/autoload.php';

const CLIENT_ID     = 'sample@codemojo.io';
const CLIENT_SECRET = 'PLB6DHP7VcykRDdvloi2X9tEq3FvsIBhtdn7UdeQ';

// Create an instance of Authentication Service
$authService = new AuthenticationService(CLIENT_ID, CLIENT_SECRET, Endpoints::SANDBOX);

// Create an instance of Loyalty Service - If you have more than one wallet service,
// you can optionally pass in the wallet instance as the second argument
$loyaltyService = new LoyaltyService($authService);

// Credit points to user
$status = $loyaltyService->addLoyaltyPoints("user1@codemojo.io", 1500, "android", "", 7, "Cashback for Order no. 1231");
```

For more details on the methods & parameters available for each service, take a look at the individual service pages.


