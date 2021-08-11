# PHP RFC: Limited User Defined Operator Overloading

## Introduction

This RFC aims to provide basic operator overloading within PHP for objects.

Objects are mostly unusable with operators currently. Using operators results in their casting to another type, and most type casting from objects is automatic and discards all information about the internal state of an object or its semantic meaning within the program. This is particularly problematic for objects which represent discrete values or sets. For such objects, there are often deterministic and repeatable state transitions which can be represented with operators effectively.

Instead, these state transitions are currently controlled via named methods. This can make code particularly difficult to read and understand if there are nested method calls, or if the object is immutable.

This RFC is an intentionally limited set of operators, a minimal set to be PHP's first usage of user defined operator overloads. The idea behind this is that the proposed operators have the most clear and impactful use cases that use a self-contained set of the operators. This allows us to take on operator overloading in chunks instead of all at once, and as we see it perform well we can consider overloading for further operators.

## Background

Operator overloading is a well explored concept in programming that enables a programmer to control the behavior of their code when combined with an infix, or operator. Some languages such as R allow for defining custom operators in addition to overloading existing operators. Some languages such as Python allow for overloading nearly all existing operators, but not for defining new operators. Some languages such as Java and PHP do not allow for custom operator overloading at all, though have operator overloading like behavior built into the language.

### Existing Operators in PHP

**Mathematical Operators**

| Symbol | Description |
| ------ | ----------- |
| **+** | Used for addition with int and float, union with arrays |
| **-** | Used for subtraction with int and float |
| **\*** | Used for multiplication with int and float |
| **/** | Used for division with int and float |
| **%** | Used for modulo with int |
| **\*\*** | Used for pow with int |

**String Operators** 

| Symbol | Description |
| ------ | ----------- |
| **.** | Used for string concatenation |

**Comparison Operators**

| Symbol | Description |
| ------ | ----------- |
| **==** | Equals comparison with type casting |
| **===** | Identity comparison |
| **>** | Greater than comparison |
| **<** | Less than comparison |
| **>=** | Greater than or equal to comparison |
| **<=** | Less than or equal to comparison |
| **!=** | Not equals comparison with type casting |
| **<>** | Not equals comparison with type casting |
| **!==** | Not identical comparison |
| **<=>** | Sort hierarchy comparison |

**Bitwise Operators**

| Symbol | Description |
| ------ | ----------- |
| **&** | Bitwise and |
| **\|** | Bitwise or |
| **^** | Bitwise xor |
| **~** | Bitwise not |
| **<<** | Bitwise shift left |
| **>>** | Bitwise shift right |

**Logical Operators**

| Symbol | Description |
| ------ | ----------- |
| **and** | Logical and operator |
| **or** | Logical or operator |
| **xor** | Logical xor operator |
| **&&** | Logical and operator |
| **\|\|** | Logical or operator |
| **!** | Logical negation operator |

**Misc. Operators**

| Symbol | Description |
| ------ | ----------- |
| **??** | Null coalesce |
| **? :** | Ternary operator |
| **@** | Error suppression operator |
| **\`\`** | Shell execution escape operator |

**Assignment Operators**

| Symbol | Example | Equivalent | Description |
| ------ | ------- | ---------- | ----------- |
| **+=** | $a += $b | $a = $a + $b | Add assignment operator |
| **-=** | $a -= $b | $a = $a - $b | Subtract assignment operator |
| **\*=** | $a \*= $b | $a = $a * $b | Multiply assignment operator |
| **/=** | $a /= $b | $a = $a / $b | Divide assignment operator |
| **%=** | $a %= $b | $a = $a % $b | Modulo assignment operator |
| **\*\*\=** | $a \*\*= $b | $a = $a \*\* $b | Pow assignment operator |
| **&=** | $a &= $b | $a = $a & $b | Bitwise and assignment operator |
| **\|=** | $a \|= $b | $a = $a \| $b | Bitwise or assignment operator |
| **^=** | $a ^= $b | $a = $a ^ $b | Bitwise xor assignment operator |
| **<<=** | $a <<= $b | $a = $a << $b | Bitwise shift left assignment operator |
| **>>=** | $a >>= $b | $a = $a >> $b | Bitwise shift right assignment operator |
| **.=** | $a .= $b | $a = $a . $b | Concatenation assignment operator |
| **??=** | $a ??= $b | $a = $a ?? $b | Null coalesce assignment operator |

### Custom Operator Behavior

There is behavior within PHP currently that could be considered operator overloading, but it is not user defined. Instead, this behavior exists to add functionality to internal implementations and structures.

**Array Unions**

Two arrays can be unioned with the `+` operator.

**DateTime**

The `DateTime` class can be used with comparison operators to determine which `DateTime` corresponds to an earlier or later value.

**Spaceship Operator**

The spaceship operator (`<=>`) has many features that would be equivalent to an operator overload from a user's perspective.

```php
<?php

// Strings
echo "a" <=> "a"; // 0
echo "a" <=> "b"; // -1
echo "b" <=> "a"; // 1

// Arrays
echo [] <=> []; // 0
echo [1, 2, 3] <=> [1, 2, 3]; // 0
echo [1, 2, 3] <=> []; // 1
echo [1, 2, 3] <=> [1, 2, 1]; // 1
echo [1, 2, 3] <=> [1, 2, 4]; // -1
```

**Extensions**

The following is a non-exhaustive list of extensions which provide objects with their own operator overloads.

- ext-decimal
- ext-gmp

## Use Cases

The following is a non-exhaustive list of use cases that this RFC in particular could serve. Further use cases exist with implementations for more operators.

### Objects Representing Arbitrary Precision Numbers

Objects which represent arbitrary precision numbers cannot convert to a float or an int in order to utilize existing operator behavior, as their values may overflow.

### Currency Values

Currency values have a numeric value along with a currency type. For instance, `$total = $usd + $yen` would need to consider not only the numeric values but the currency conversion between the two, and what currency the return value should be in. In same cases, an application may want to automatically handle these decisions, however in other situations it may way to throw an exception unless the currency types match.

Further, the expression `$total = $usd * $yen` is nonsensical, while the expression `$total = $usd * 5` has meaning, showing that intelligent type controls may be a necessary feature for some user applications.

### Complex Numbers

Objects which represent complex numbers cannot be meaningfully converted to any scalar and then used with existing operator behavior. Further, the behavior of certain operators implies complex calculations. For instance, `(1 + 2i) * (3 + 4i)` must be solved by using the FOIL method: First, Outside, Inside, Last. The correct value for the expression is `-5 + 10i`, but it can also be expressed by the tuple `(5*sqrt(5), PI - atan(2))` if given as polar values instead.

### Matrices and Vectors

Matrices have different behavior when combined using an operator with a simple numeric value versus another matrix. Matrices also exhibit non-commutative behavior with multiplication. Commutativity is covered in the next section in greater detail.

### Unit Based Values

Values which have an associated unit have additional logic when used with operators. The currency example above is a specific case of this general class. There are three main considerations that need to be made when a value has a unit of some kind:

1. Are the units of the two values compatible with the desired operation.
2. Do any unit conversions need to be done before the operation can be performed.
3. Is there a specific unit that it makes the most sense to give the return value in.

For instance, the expression `19cm + 2m` could be expressed as `219cm` or `2.19m` or `2190mm`. Meanwhile, the expression `3m * 4m` would be expressed as `12m^2`. The expression `1.2km + 4mi` would require a unit conversion before the operation could be performed. And the expression `2m + 4L` doesn't have a meaningful answer.

Unit based values in particular benefit from allowing user-defined typing in the arguments.

## Benefits Over Named Methods

There are several benefits for objects using operators over named method calls where it makes sense to do so.

### Operators Promote Immutability

Operators behave immutably in PHP, returning the modified value without changing the variables referenced. Since objects can change state in any scope in which they are accessed, mutable objects can sometimes lead to unexpected behavior in programs that have many scopes with access to an object. While there are limited ways to guarantee immutability within the operator overload methods themselves, this expectation of immutable behavior would promote immutable behavior of objects in PHP, at least for the operator overload methods.

### More Readable Code

Consider a number object which represents an integer or float. Below are two usages of such an object, one which uses named methods, and one which uses operator overloads. Both implement the Quadratic Formula.

**Named Methods**
```php
<?php

$a = new Number(8);
$b = new Number(6);
$c = new Number(4);

$posRoot = $b->mul(-1)->add($b->pow(2)->sub($a->mul($c)->mul(4))->sqrt())->div($a->mul(2));

$negRoot = $b->mul(-1)->sub($b->pow(2)->sub($a->mul($c)->mul(4))->sqrt())->div($a->mul(2));
```

**Operator Overloads**
```php
<?php

$a = new Number(8);
$b = new Number(6);
$c = new Number(4);

$posRoot = ((-1 * $b) + ($b ** 2 - 4 * $a * $c)->sqrt()) / (2 * $a);

$negRoot = ((-1 * $b) - ($b ** 2 - 4 * $a * $c)->sqrt()) / (2 * $a);
```

### Precedence Through Operators vs. Nesting

The primary thing that made the above example harder to read with named methods was the need to nest method calls in order to create the correct order of operations. Operators have an established precedence that is well understood and expected however, allowing nested method calls to be replaced with operators in a way that makes the order of execution more clear.

### Reduces Need For Scalar Objects

Scalar objects has been a fairly consistently requested feature from PHP developers for a while now. The main reason that userland solutions to scalar objects have been seen as lackluster is the extremely poor interaction with operators that objects demonstrate, particularly with comparisons, logical operators, and for numeric types mathematical operators.

Though logical operators are not included in this RFC, the inclusion of operator overloads reduces the need for scalar objects in core, which has been explored by internals previously, but constitutes a large effort to actually implement. With operator overloads, all the tools necessary to create full scalar objects would be available to PHP developers to create their own implementations, further sidestepping the issues surrounding varied opinions on the public API of such objects that would surely ensue in a serious effort to create them.

## Commutativity

Commutativity refers to the ability of operands to be reversed while retaining the same result. That is:

```php
<?php

$a + $b === $b + $a;
```

Some operators are always commutative in PHP currently, while others are not:

```php
<?php

// Arithmetic
$a + $b === $b + $a; // This is true for int + int, int + float, float + int, and array + array
$a - $b !== $b - $a; // Subtraction by definition is not commutative
$a * $b === $b * $a; // This is true for numeric types
$a / $b !== $b / $a; // Division by definition is not commutative

// Other Math
$a % b !== $b % $a; // Modulo by definition is not commutative
$a ** $b !== $b ** $a; // Pow by definition is not commutative
```

The mathematical rules for commutativity depend on the type of mathematical object which is being considered. All integers and floats fall within the real numbers, and so natively both the `+` and `*` operators are commutative for all values because this is a property of real numbers.

However, for other mathematical objects rules for commutativity are different. Considering matrices, multiplication is no longer commutative.[1] Thus **enforcing the existing commutative behavior may enforce incorrect behavior on user code**. For this reason, this RFC makes no effort to enforce commutativity, as doing so will in reality introduce bugs to the behavior of the operators for various domains.

There is more argument for enforcing commutativity for the **logical operators**, which definitionally should be commutative if they are used as logical operators. Doing so would preclude libraries and user applications from repurposing the logical operators for another purpose in some circumstances. However, as that is not part of this RFC and is left as future scope, it has no impact on this proposal.

## Operator Overloads in Other Languages

There are three main approaches to operator overloading in other languages.

### R

R has operator overloads implemented through named infixes. This means that not only can all operators be overloaded in R, but additionally new operators can be defined. This approach can lead to some codebases containing custom operators. Such operators are designated as: `%infix%`. For example:

```R
> `%addmul%` <- function(x,y) (x+y)*y
> 2 %addmul% 3
[1] 15
```

This style of operator overloading is more suited to purely functional languages and is not being considered for this RFC.

### Python

Python's implementation of operator overloads most closely matches this proposal, and so for the purpose of comparison we will more closely consider Python's operator overloading.

In Python, all operators can be overloaded except for the logical operators. These are provided in two groups:

**Rich Comparison**

The comparison operators are implemented as a single method each, with a default implementation for `==` and `!=`. Rich comparison operators are not commutative in Python. For example, if you have two objects `x` and `y`, then the following will happen for this comparison `x >= y`:

1. If `x` implements `__ge__`, then `x.__ge__(x, y)` will be called.
2. If `x` does not implement `__ge__` then precedence falls to `y`.
3. If `y` implements `__le__`, then `y.__le__(y, x)` will be called.
4. If `y` does not implement `__le__` then the default operator behavior is used.

NOTE: Python actually gives precedence to `y` in the above example if `y` is a direct or indirect subclass of `x`.

In Python, the comparison operators are not directly commutative, but have a reflected pair corresponding with the swapped order. However, each object could implement entirely different logic, and thus no commutativity is enforced.

**Numeric Operators**

The numeric operators (including the bitwise operators) are implemented each as a family of three overrides: `__op__`, `__iop__`, and `__rop__`.

The `__op__` method is called when the object is the left operand of an operator. The `__rop__` method is called when the object is the right operand of an operator. The `__iop__` method is called when the corresponding reassignment operator is called (always on the assigned object).

It is easy to see from this set of implementations that not only is commutativity not preserved, but full support for breaking commutativity in a controlled and intelligent way is supported.

### Java/PHP

Java does not support user-defined operator overloads at all, but has built-in behavior that is similar to operator overloads for certain situations. PHP has similar behavior, with not current support for user-defined operator overloads, but with some built-in behavior that acts in ways similar to an operator overload. The PHP cases were covered in the **Current Operator Behavior** section of the **Background**.

In Java, the `+` operator can be used to join strings in a way similar to the PHP operator `.` and is sometimes described as a "built-in operator overload" by Java developers.

## Proposal

This RFC proposes adding magic methods to PHP to control operator overloading for a limited set of operators. This RFC only proposes overloads for two part operations, or stated differently, no unary operations are proposed for inclusion in this RFC. As such, all the magic methods fit a single general format:

```php
<?php

Class A {
  
  public function __op(mixed $other, bool $left): mixed {
  }
  
}
```

The second operand is always pass as `$other` regardless of whether the called object is the left or right operand. If the called object is the left operand, then `$left` is `true`. If the called object is the right operand, then `$left` is `false`.

A new exception, `InvalidOperator`, is also provided for users to throw within the operator magic methods if for any reason the operation is invalid due to type constraints, value constraints, or other factors.

The default types for `$other` and the return are `mixed`, to allow user code to further narrow the type as appropriate for their application.

### Comparison Operators

Partial support for comparison operators is also part of this RFC. Comparison operators in Python **do not** restrict the return to a boolean value. While there may be many use cases for overloading the comparison operators in a way that does not return a boolean, in the interest of promoting consistency with existing code, the magic methods for the comparison operators have the additional restriction of returning `bool` instead of `mixed`.

Additionally, since comparisons have a reflection relationship instead of a commutative one, the `$left` argument is omitted as it doesn't make sense. They can also still throw exceptions, including the `InvalidOperator` exception.

```php
<?php

Class A {

  public function __comparisonOp(mixed $other): bool {
  }

}
```

As the comparison operators involve a reflection relationship instead of a commutative one, the same behavior as detailed in the Python section will be used.

| Left Operand Method | Right Operand Method |
| ------------------- | -------------------- |
| `__lessThan()` | `__greaterThan()` |
| `__greaterThan()` | `__lessThan()` |
| `__lessThanOrEq()` | `__greaterThanOrEq()` |
| `__greaterThanOrEq()` | `__lessThanOrEq()` |
| `__equals()` | `__equals()` |
| `__notEquals()` | `__notEquals()` |

The spaceship operator (`<=>`), used to determine sort hierarchy and encompassing all comparisons for numeric values, is also supported. However, its reflection and definition is slightly different:

```php
<?php

Class A {

  public function __compareTo(mixed $other): int {
  }

}
```

| Left Operand Method | Right Operand Method |
| ------------------- | -------------------- |
| `__compareTo()` | `__compareTo() * -1` |

### Supported Operators

In this RFC only a subset of the operators in PHP are supported for operator overloading. The proposed operators are:

**Math Operators**

| Operator | Method Name |
| -------- | ----------- |
| `+` | `__add()` |
| `-` | `__sub()` |
| `*` | `__mul()` |
| `/` | `__div()` |
| `%` | `__mod()` |
| `**` | `__pow()` |

**Comparison Operators**

| Operator | Method Name |
| -------- | ----------- |
| `>` | `__greaterThan()` |
| `<` | `__lessThan()` |
| `>=` | `__greaterThanOrEq()` |
| `<=` | `__lessThanOrEq()` |
| `==` | `__equals()` |
| `!=` | `__notEquals()` |
| `<=>` | `__compareTo()` |

## Backward Incompatible Changes

Objects used with one of the operators that support operator overloads will now throw an `InvalidOperator` exception if the operator method is not implemented on the object or objects in question. Since expressions involving objects and these operators prior have been mostly nonsensical before, the anticipated impact of this is minimal.

## Proposed PHP Version

This change is proposed for PHP 8.2

## RFC Impact

### To SAPIs

None

### To Existing Extensions

Existing extensions can continue to define their own operator overloads by providing a `do_operation` call for their classes, however classes which are open to be extended may need to be updated so that their overloads can be extended by implementing the necessary methods.

### To Opcache

None

### New Constants

None

### php.ini Defaults

None

## Future Scope

Many things that could be part of this RFC are left to future scope.

### Further Operator Support

The bitwise operators, string concatenation operator, logical operators, and a few of the miscellaneous operators also could potentially benefit from operator overloads. In particular, bitwise operators could be combined with enums in interesting ways to provide enum pseudo-sets.

### Reassignment Operators

The reassignment operators are optimized as part of the compile step to instances of the base operators. If control of reassignment operators independently of the associated plain operators were to be supported, changes to how this optimization is done would be needed.

### Scalar Objects

This RFC could impact and make the often explored scalar objects concept more fully featured, or even unneeded. It could, alternatively, make ensuring their behavior more difficult. Either way it is likely that this RFC would affect the scope of any scalar objects RFC.

### Exposing Core Overloads

As mentioned in this RFC, there are some objects within core that implement their own limited operator overloads. Deciding whether to update these objects and open their overloads for extension is left as future scope.

### New Infixes

This RFC does not support R-style operator overloading, which allows users to define custom operators outside the symbol set supported by core. Such a feature would be part of a separate RFC.

## Proposed Voting Choices

Add limited user-defined operator overloads as described: yes/no. A 2/3 vote is required to pass.

## Vote

## Patches and Tests

https://github.com/JordanRL/php-src/commit/58a1456064de9b210364d2f78c9e9b6a72bd6c03

## References

[1]: https://en.wikipedia.org/wiki/Matrix_multiplication#Non-commutativity

## Changelog
