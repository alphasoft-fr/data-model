# Model Class
[![Latest Stable Version](http://poser.pugx.org/alphasoft-fr/data-model/v)](https://packagist.org/packages/alphasoft-fr/data-model) [![Total Downloads](http://poser.pugx.org/alphasoft-fr/data-model/downloads)](https://packagist.org/packages/alphasoft-fr/data-model) [![Latest Unstable Version](http://poser.pugx.org/alphasoft-fr/data-model/v/unstable)](https://packagist.org/packages/alphasoft-fr/data-model) [![License](http://poser.pugx.org/alphasoft-fr/data-model/license)](https://packagist.org/packages/alphasoft-fr/data-model) [![PHP Version Require](http://poser.pugx.org/alphasoft-fr/data-model/require/php)](https://packagist.org/packages/alphasoft-fr/data-model)
## Installation

Use [Composer](https://getcomposer.org/)

### Composer Require
```
composer require alphasoft-fr/data-model
```

## Requirements

* PHP version 7.3

## Usage

### Creating a Model

To create a new model, extend the `Model` class and implement the required abstract methods: `getDefaultAttributes` and `getDefaultColumnMapping`.

```php
use AlphaSoft\DataModel\Model;

class UserModel extends Model
{
    protected static function getDefaultAttributes(): array
    {
        return [
            'fullName' => null,
            'age' => null,
            'isActive' => true,
        ];
    }

    protected static function getDefaultColumnMapping(): array
    {
        return [
            'fullName' => 'full_name',
            'age' => 'user_age',
        ];
    }
    
    public static function getPrimaryKeyColumn(): string
    {
        return 'id'; // Replace 'id' with the actual primary key column name
    }
}
```

### Hydrating Data

You can create a new model instance and hydrate it with data using the constructor or the `hydrate` method.

```php
$data = [
    'full_name' => 'John Doe',
    'user_age' => 30,
    // ...
];

$user = new UserModel($data);

// Alternatively
$user = new UserModel();
$user->hydrate($data);
```

### Getting and Setting Attributes

You can access attributes using getter and setter methods. Attributes are automatically mapped to their corresponding columns based on the column mapping configuration.

```php
$fullName = $user->getString('fullName');
$age = $user->getInt('age');

$user->set('age', 31);
$user->set('isActive', false);
```

### Converting to Array

You can convert a model to an array using the `toArray` method.

```php
$userArray = $user->toArray();
```

### Using with PDO

Suppose we have a `UserModel` class based on the `Model` class, and we want to manage user data in a database.

### Example: Inserting Data

```php
use YourNamespace\UserModel;
use PDO;

// Connect to the database
$pdo = new PDO("mysql:host=localhost;dbname=yourdb", "username", "password");

// Create a new UserModel instance
$user = new UserModel();
$user->set('fullName', 'Jane Smith');
$user->set('age', 25);

// Insert the new user data into the database
$columns = implode(', ', array_keys($user->toDb()));
$values = ':' . implode(', :', array_keys($user->toDb()));
$sql = "INSERT INTO users ($columns) VALUES ($values)";
$stmt = $pdo->prepare($sql);
foreach ($user->toDb() as $column => $value) {
    $stmt->bindValue(":$column", $value);
}
$stmt->execute();

$insertedPrimaryKeyValue = $pdo->lastInsertId();
echo "User inserted with primary key: $insertedPrimaryKeyValue\n";
```

### Example: Updating Data

```php
use YourNamespace\UserModel;
use PDO;

// Connect to the database
$pdo = new PDO("mysql:host=localhost;dbname=yourdb", "username", "password");

// Retrieve user data from the database
$primaryKeyValue = 1; // Replace with the primary key value of the user you want to update
$sql = "SELECT * FROM users WHERE " . UserModel::getPrimaryKeyColumn() . " = :primaryKeyValue";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':primaryKeyValue', $primaryKeyValue);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Create an instance of UserModel
$user = new UserModel($data);

// Modify object attributes
$user->set('fullName', 'Jane Smith');
$user->set('age', 25);

// Update the changes in the database
$updates = [];
foreach ($user->toDb() as $column => $value) {
    $updates[] = "$column = :$column";
}
$sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE " . UserModel::getPrimaryKeyColumn() . " = :" .UserModel::getPrimaryKeyColumn();
$stmt = $pdo->prepare($sql);
foreach ($user->toDb() as $column => $value) {
    $stmt->bindValue(":$column", $value);
}
$stmt->execute();

echo "User updated with primary key: $primaryKeyValue\n";
```

I hope these examples meet your requirements. If you have any further questions or need more assistance, feel free to ask!


## Available Methods

The `Model` class provides the following methods to manipulate object data:

- `hydrate(array $data)`: Hydrates the object with the provided data.
- `toArray()`: Converts the object to an associative array.
- `get(string $property)`: Retrieves the value of an object property.
- `set(string $property, $value)`: Sets the value of an object property.
- `toDb()`: Converts the object to an associative array ready for database insertion.

#### Type-Specific Retrieval

You can also retrieve attribute values with specific data types using dedicated methods. These methods provide type-checking and do not allow for default values when the property is not defined or if the value is of the wrong type.

- `getString` retrieves a string value.

```php
$lastname = $user->getString('lastname', 'Doe'); // Retrieves 'Doe' if 'lastname' exists and is a string
```

- `getInt` retrieves an integer value.

```php
$age = $user->getInt('age', 25); // Retrieves 25 if 'age' exists and is an integer
```

- `getFloat` retrieves a floating-point value.

```php
$price = $product->getFloat('price', 0.0); // Retrieves 0.0 if 'price' exists and is a float
```

- `getBool` retrieves a boolean value.

```php
$isActive = $user->getBool('isActive', false); // Retrieves false if 'isActive' exists and is a boolean
```

- `getArray` retrieves an array.

```php
$tags = $post->getArray('tags', []); // Retrieves an empty array if 'tags' exists and is an array
```

- `getInstanceOf` retrieves an instance of a specified class, or null if it exists and is an instance of the specified class.

```php
$profile = $user->getInstanceOf('profile', Profile::class); // Retrieves an instance of Profile or null if 'profile' exists and is an instance of Profile
```

- `getDateTime` retrieves a `DateTimeInterface` instance, optionally specifying a format for parsing.

```php
$createdAt = $post->getDateTime('created_at', 'Y-m-d H:i:s'); // Retrieves a DateTimeInterface instance or null if 'created_at' exists and is convertible to a valid date
```

Please note that these methods will throw exceptions if the property is not defined or if the value is of the wrong type. If you want to allow default values, you can use the previous examples with default values, but they will not throw exceptions in those cases.

## Configuring Attributes and Columns

To configure the attributes and columns of your model, you need to implement the following abstract methods in your model class:

- `getDefaultAttributes()`: Defines the default attributes of the object.

This method should be implemented to return an associative array representing the default attributes of the object, including their default values.

For example:

```php
protected static function getDefaultAttributes(): array
{
    return [
        'id' => null,
        'fullName' => null,
        'age' => 0,
        'isActive' => true,
    ];
}
```

- `getDefaultColumnMapping()`: Defines the mapping between object properties and database columns.

This method should be implemented to return an associative array that maps object properties to their corresponding database columns.

For example:

```php
protected static function getDefaultColumnMapping(): array
{
    return [
        'fullName' => 'full_name',
        'age' => 'user_age',
        'isActive' => 'is_active',
    ];
}
```

- `getPrimaryKeyColumn()`: Get the name of the primary key column for the model.

For example:

```php
public static function getPrimaryKeyColumn(): string
{
    return 'id'; // Replace 'id' with the actual primary key column name
}
```

This method should be implemented by subclasses to return the name of the column that serves as the primary key for the model's corresponding database table.

## Method `getPrimaryKeyValue()`

The `getPrimaryKeyValue()` method is a utility function that allows you to retrieve the value of the primary key column for the model object. This method is particularly useful when you need to fetch the primary key value of the model object for operations such as updates or deletions in the database.

This method directly utilizes the `get()` method to retrieve the value of the primary key column, based on the configured primary key column name.

Here's an example illustrating the usage of the `getPrimaryKeyValue()` method within the context of a model class:

```php
use AlphaSoft\DataModel\Model;

class UserModel extends Model
{
    protected static function getDefaultAttributes(): array
    {
        return [
            'id' => null,
            'fullName' => null,
            'age' => null,
            'isActive' => true,
        ];
    }

    protected static function getDefaultColumnMapping(): array
    {
        return [
            'id' => 'user_id',
            'fullName' => 'full_name',
            'age' => 'user_age',
            'isActive' => 'is_active',
        ];
    }
    
    public static function getPrimaryKeyColumn(): string
    {
        return 'id'; // Replace 'id' with the actual primary key column name
    }
}

// ...

// Creating a UserModel instance
$userData = [
    'id' => 1,
    'fullName' => 'John Doe',
    'age' => 30,
    'isActive' => true,
];
$user = new UserModel($userData);

// Getting the primary key value
$primaryKeyValue = $user->getPrimaryKeyValue();
echo "Primary Key Value: $primaryKeyValue\n"; // Output: Primary Key Value: 1
```

In this example, `getPrimaryKeyValue()` method directly retrieves the value of the primary key column (in this case, the user's ID) from the model object using the `get()` method. This is a convenient way to obtain the primary key for subsequent operations, such as updating or deleting data in the database.

As always, ensure that you adjust the values and column names to match your specific model and database configuration.

## ModelFactory

The `ModelFactory` class provides convenient methods to create instances of your model classes and collections from arrays of data. This can be particularly useful for scenarios where you need to transform raw data into fully hydrated model instances.

### Usage

First, include the necessary namespace for the `ModelFactory` class at the top of your file:

```php
use AlphaSoft\DataModel\Factory\ModelFactory;
```

#### Creating a Single Model Instance

You can use the `createModel` method of the `ModelFactory` class to create a single instance of your model using an array of data:

```php
$modelData = [
    'fullName' => 'John Doe',
    'age' => 30,
    // ... other properties
];

$model = ModelFactory::createModel(YourModelClass::class, $modelData);
```

Replace `YourModelClass` with the actual class name of your model.

#### Creating a Collection of Model Instances

If you have an array of data representing multiple models, you can use the `createCollection` method to create a collection of model instances:

```php
$collectionData = [
    // ... array of model data
];

$collection = ModelFactory::createCollection(YourModelClass::class, $collectionData);
```

Replace `YourModelClass` with your actual model class name.

### Example

Here's an example demonstrating how to use the `ModelFactory` class to create model instances:

```php
use AlphaSoft\DataModel\Factory\ModelFactory;
use YourNamespace\UserModel;

// Sample data for a single model instance
$modelData = [
    'fullName' => 'Jane Smith',
    'age' => 25,
    // ... other properties
];

// Create a single model instance
$model = ModelFactory::createModel(UserModel::class, $modelData);

// Sample data for a collection of model instances
$collectionData = [
    // ... array of model data
];

// Create a collection of model instances
$collection = ModelFactory::createCollection(UserModel::class, $collectionData);

// Use the $model and $collection objects as needed
```

Remember to adjust the namespace and class names to match your actual project structure.

### Note

Before using the `ModelFactory` class, ensure that your model classes are set up to work seamlessly with it. The `ModelFactory` assumes that your models extend the `Model` class and are designed to be hydrated from arrays of data.

## License

This package is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).
