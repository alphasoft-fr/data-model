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

```php
use YourNamespace\UserModel;
use PDO;

// Connect to the database
$pdo = new PDO("mysql:host=localhost;dbname=yourdb", "username", "password");

// Fetch data from the database
$statement = $pdo->query("SELECT * FROM users WHERE id = 1");
$data = $statement->fetch(PDO::FETCH_ASSOC);

// Create an instance of UserModel
$user = new UserModel($data);

// Modify object attributes
$user->set('fullName', 'Jane Smith');
$user->set('age', 25);

// Save changes to the database
$dbData = $user->toDb();

// Prepare insertion query
$columns = implode(', ', array_keys($dbData));
$values = ':' . implode(', :', array_keys($dbData));
$statement = $pdo->prepare("INSERT INTO users ($columns) VALUES ($values)");

// Execute the query with object data
$statement->execute($dbData);
```
## Available Methods

The `Model` class provides the following methods to manipulate object data:

- `hydrate(array $data)`: Hydrates the object with the provided data.
- `toArray()`: Converts the object to an associative array.
- `get(string $property)`: Retrieves the value of an object property.
- `set(string $property, $value)`: Sets the value of an object property.
- `toDb()`: Converts the object to an associative array ready for database insertion.

## Configuring Attributes and Columns

To configure the attributes and columns of your model, you need to implement the following abstract methods in your model class:

- `getDefaultAttributes()`: Defines the default attributes of the object.
- `getDefaultColumnMapping()`: Defines the mapping between object properties and database columns.

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
