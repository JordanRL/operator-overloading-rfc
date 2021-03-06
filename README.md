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
| **->** | Object accessor |
| **?->** | Null-safe object accessor |
| **\[\]** | Array referrence |

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

The following is a non-exhaustive list of use cases that this RFC in particular could serve. Further use cases exist with implementations for more operators. This is an attempt to catalogue the likely use cases after the feature is introduced, not suggest that each of these represented a correct usage of this feature.

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

### Scalar Objects & Scalar Expansions

The availability of the math operators will enable scalar object implementations in user code that actually act like scalars in most circumstances. This is further discussed in the benefits and future scope sections.

### Collections

Collection objects could use the `+` and `/` operators as union and partition operators. The union behavior is already supported for arrays natively, and would allow collection objects to benefit from it as well.

### Resource Control Objects

Some objects which represent a resource might use operator overloading as well. For instance, using the `+` operator to append a string to a file. This usage would be prone to errors and instability, but in the interests of being complete, it is a foreseeable usage.

### Queues

Much like collections, queues could use operator overloads to control adding and removing items from the queue.

## Benefits

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

### Brings User Code More In Line With Internals

Right now extensions and code in the engine have access to operator overloads while user code does not. This RFC brings user code more in line with the tools that are available in core by providing some access to control over object interaction with operators.

### Drop In Type Replacements

For applications that start with a numeric scalar type and then later realize that it needs to be replaced with an object due to limitations of the scalar type, doing so represents a total refactor of the application. This may happen if you find that the `int` needs to keep track of unit conversion, or if you find that the value can overflow the type.

With operator overloads, you could replace the variable initialization without refactoring the rest of the code.

## Risks

In addition to benefits the feature provides, there are also risks the feature must address.

### Less Readable Code With Poor Implementations

Not all implementations in user code will follow best practices or understand the impact of their design choices. In such cases, operator overloading can make it less clear what a program is doing by hiding the complexity with an implied method call instead of an explicit one. These risks are not unique to operator overloading however, as there are many features of PHP which can make extremely unreadable code if used incorrectly.

For example, the __toString() magic method is currently called in combination with the concat operator `.` and there is no restriction for this method to only execute code related to providing a string representation. It is entirely possible right now for this method to be used to mutate the object, resulting in unexpected and obfuscated behavior when an object is concatenated.

This does not appear to be a widespread problem in PHP codebases however, and so while this is still a possible risk, the RFC author does not view it as any more risky than continuing to support existing magic methods.

### Code May Be Non-Equivalent In Separate Codebases

A user who copy and pastes a code snippet that takes advantage of operator overloading may end up with a non-equivalent program in their own codebase if they do not also include the class definitions that are referenced in the original source. While this may be unexpected in some circumstances, in most cases this is true currently of any code portability considerations a user may encounter.

For example, a call to a database abstraction layer may be unequivalent if the configuration or schema of the underlying database is different in different codebases. A call to a class will already behave different if that class is replaced with a different definition, this is simply an exension of that reality. 

The main risk is that changing a class definition prior would only have an effect on method calls (in most circumstances). This isn't 100% true, as demonstrated in the previous section, since existing magic methods provide ways for objects to mutate or affect behavior of non-method-call lines. This risk would be reduced by this RFC through three main strategies:

1. An object used with an operator that is not defined on the class will result in an `InvalidOperator` exception immediately, preventing the issue from being deferred until a later and helping the problem be caught early.
2. While currently expressions such as `$val = $obj + 2` will silently cast, through correctly typing the arguments users can be provided with early and explicit type errors and exceptions that make it easier to spot such issues.
3. By allowing typed arguments for the operator overload methods, non-sensical object combinations can be avoided. Since this is controlled mainly through typing, it forces the developer to white list the classes that the operator works with.

### Operators Can Hide Non-Equivalent Substitutions

While commutativity is addressed at much greater length in the next section, it is possible for the following lines to be non-equivalent:

```php
<?php

$int = new Number(5);
$dec = new Decimal(6.0);

$val1 = $int + $dec;
$val2 = $dec + $int;
```

In may not be entirely clear that these two lines are not equivalent. Assuming that both classes have the method defined, the first line will result in:

```php
<?php

$val1 = $int->__add($dec, true);
```

While the second line will result in:

```php
<?php

$val2 = $dec->__add($int, true);
```

Ultimately this is an issue mostly for the `+` and `*` operators, since the other math operators are already non-commutative. If the developer does not understand that using objects can make the `+` and `*` operators non-commutative as well, bugs may be introduced that are not caught early.

This can again be mitigated through good argument typing and design. A class can simply only accept other classes as arguments that it can ensure commutativity for. Or in some cases, such commutativity violations are actually part of the feature, such as with matrices.

### Use With Structured Representations

Though some people will surely see this as a benefit, objects which represent things such as **Collections** or **Resources** will likely take advantage of some oeprator overloads. These are mentioned in the **Use Cases** section as it would be naive to think that they won't happen.

This risk is mitigated in this RFC by limiting the operators which can be overloaded and by having *Implied Overloads*. These are overloads that occur due to engine and compiler optimizations. For instance, a Queue might decide it wants to use the `-` operator to pull things from the queue, perhaps with the convention of pulling `int` items from the queue in the format `$queue - int`.

This would work fine for a normal usage of this operator, but would create the odd circumstance of the queue being reassigned when used with `-=`. This behavior would be unavoidable with the proposed implementation, and would thus discourage non-reflexive usages of operator overloads:

```php
<?php

$queue = new Queue();

$queue += new Item(); // With the + overload used to push
$queue += new Item();

$first = $queue - 1; // With the - overload used to pop
$queue -= 1; // This would be evaluated as $queue = $queue - 1
             // Support for this would be unavoidable for the queue
             // Discouraging usage of the operator in this manner
```

Additionally, this unavoidable relationship between the implemented and implied operator overloads would mean that if an operator overload was used in this way anyway, the implementer would need to communicate the dangers of the overload very explicitly and loudly, which promotes building community behavior around safe usage of operator overloads by promoting community standards.

## Properties of Operators in General

### Commutativity

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

### Associativity

Associativity refers to the ability of operands to be grouped or evaluated with arbitrary precedence and result in the same value:

```php
<?php

($a + $b) + $c === $a + ($b + $c);
```

It has similar behavior to commutativity in PHP with regard to operators:

```php
<?php

// Arithmetic
($a + $b) + $c === $a + ($b + $c); // True
($a - $b) - $c !== $a - ($b - $c); // Subtraction by definition is not associative
($a * $b) * $c === $a * ($b * $c); // True
($a / $b) / $c !== $a / ($b / $c); // Division by definition is not associative

// Other Math
($a % $b) % $c === $a % ($b % $c); // Modulo by definition is not associative
($a ** $b) ** $c === $a ** ($b ** $c); // Pow by definition is not associative
```

Associativity faces a similar problem to commutativity. Addition and multiplication are associative over the real numbers, and as `int` and `float` are part of the reals, the `+` and `*` operators are currently associative for all PHP code. This behavior cannot be guaranteed however with objects which use operator overloading. In particular, `$a` and `$b` may be entirely different classes that accept different types as arguments.

### Retry Attempts vs. Errors

While such behavior may be desired in some cases, the general idea behind this feature is that the engine should make as few assumptions as possible about the nature of the operation being performed. This is because the engine will have limited ability to infer the purpose intended of an operator overload, while the objects in question will have a full understanding of object state, program state, and context.

Because of this, the general position of this RFC, which covers both commutativity and associativity, is that magic should not be performed in order to minimize errors. Instead, using operator overloads should produce errors any time the operation cannot be completed *and* the engine must make assumptions about the program in order to avoid the error.

Taking this position means that errors resulting from associativity and commutativity issues should not be avoided. Instead, they should be thrown as early as possible to help the PHP developer avoid as much poor usage of the feature as possible.

The exception to this position is when an object doesn't implement the operator. The engine *will* retry the operation if the left operand doesn't support the operation in question at all by not implementing the relevant interface. In such a case, it will check the right operand for an implementation of the relevant interface. This will ensure commutativity for cases of the type:

```php
<?php

$num = new Number(5);

$val1 = $num + 1;
$val2 = 1 + $num;
```

This will help keep object interaction with scalars consistent after this RFC is implemented.

## Operator Overloads in Other Languages

There are three main approaches to operator overloading in other languages.

### R

R has operator overloads implemented through named infixes. This means that not only can all operators be overloaded in R, but additionally new operators can be defined. This approach can lead to some codebases containing custom operators. Such operators are designated as: `%infix%`. For example:

```R
> `%addmul%` <- function(x,y) (x+y)*y
> 2 %addmul% 3
[1] 15
```

This style of operator overloading is more suited to purely functional languages and is not being considered for this RFC. Beyond the questions of whether or not such a feature is appropriate for PHP, implementing arbitrary infixes would be a much more severe change to the engine that the proposer is not willing to undertake. Further, it would make ensuring things such as consistency between comparison operators much more challenging, if not impossible.

Such strategies to operator overloading generally make sense when they are global, instead of specific to a particular object. As this RFC deals with object-specific operator overloads, this strategy is deemed inappropriate

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

Java does not support user-defined operator overloads at all, but has built-in behavior that is similar to operator overloads for certain situations. PHP has similar behavior, with no support for user-defined operator overloads but with some built-in behavior that acts in ways similar to an operator overload. The PHP cases were covered in the **Current Operator Behavior** section of the **Background**.

In Java, the `+` operator can be used to join strings in a way similar to the PHP operator `.` and is sometimes described as a "built-in operator overload" by Java developers.

## Design Considerations

There are several deliberate design considerations that were made to possible issues and risks that operator overloads present.

### The Identity Operator Is Not Overloadable

For a variety of reasons it would be problematic to allow operator overloading of the identity operator. This operator is supposed to guarantee type and that property would be lost the moment it was possible to overload the operator. Consider the following:

```php
<?php

class A {
  public int $value;
  
  public __equals(int|float|A $other, bool $left): bool
  {
    if ($other instanceof A) {
      return $this->value == $other->value;
    }
    
    return $this->value == $other;
  }
}

$notInt = new A();
$notInt->value = 100;

if ($notInt == 100) {
  echo "Equivalent to (int)100";
}

if ($notInt === 100) {
  echo "Identical to (int)100";
}
```

An object can be equivalent to other types, and that equivalence is something that the application itself should define. However an object cannot be identical to anything except itself, and allowing such an overload would enable objects to "lie" in ways that would be unintuitive to most programmers while not really enabling any new functionality.

### Logical Operators Are Not Overloadable

This was another deliberate design choice. The logical operators **&&**, **||**, **and**, **or**, and **xor** have a critical and specific meaning within PHP. These operators refer to a specific kind of math operation, boolean algebra, and their usage is reserved for that purpose only. It would be far more disruptive and ambiguous to overload these operators than most other operators.

Most behavior that users would want to control with overloads to these operators can in fact be accomplished by allowing an object to control its casting to a boolean value. That is not part of this RFC, but the RFC author views that as a better way to address these operators than allowing arbitrary overloads.

### Error Early Where Possible

The proposed implementation errors as early as possible to help developers who use operators in unsupported ways. This is covered in the **Backwards Compatibility** section as well, but part of this is that **all objects will error when used with one of the overloadable operators unless they implement the corresponding overload**. The forced use of typing for the arguments will also ensure that unhandled types error immediately with a **TypeError**.

### Static vs. Dynamic Methods

With the presence of static properties, a static method on an object does not guarantee immutability. However, having static methods for operator overloads might communicate to developers that these are intended to be used immutably. On the other hand, having the overload be static would make it much more difficult to use operator overloads with protected or private class properties.

As it seems that mutable behavior from operator overloads would be widely seen by PHP developers as a bug in most circumstances, and the reassignment operators are not given independent overload methods, this RFC proposes using dynamic methods to make use of protected and private properties easier and more intuitive.

## Proposal

### Typed Arguments

Contrary to previous proposals, type errors due to argument type mismatches are not suppressed or translated to a different value, and instead allowed to function as exceptions or errors normally would. To facilitate this, the operand is typed in the interfaces as having the type `never`. As this is a bottom type, it allows any type expansion needed in the implementation while preserving contravariance. Additionally, as the `never` type is fairly useless for an argument type, this will force all implementers to be deliberate about the typing the operator overload accepts.

### Unimplemented Operator Methods

If an operator is used with an object which does not have an implementation of the interface for that operator, an `InvalidOperator` exception is thrown.

### Binary Operator Methods

This RFC proposes adding magic methods to PHP to control operator overloading for a limited set of operators. This RFC only proposes overloads for two part operations, or stated differently, no unary operations are proposed for inclusion in this RFC. As such, all the magic methods fit a single general format:

```php
<?php

interface Opable {
  
  public function __op(never $other, bool $left): mixed;
  
}
```

The second operand is always passed as `$other` regardless of whether the called object is the left or right operand. If the called object is the left operand, then `$left` is `true`. If the called object is the right operand, then `$left` is `false`.

A new exception, `InvalidOperator`, is also provided for users to throw within the operator magic methods if for any reason the operation is invalid due to type constraints, value constraints, or other factors.

The default types for `$other` and the return are `mixed`, to allow user code to further narrow the type as appropriate for their application.

### Comparison Operator Methods

Partial support for comparison operators is also part of this RFC. Comparison operators in Python **do not** restrict the return to a boolean value. While there may be many use cases for overloading the comparison operators in a way that does not return a boolean, in the interest of promoting consistency with existing code, the magic methods for the comparison operators have the additional restriction of returning `bool` instead of `mixed`.

Additionally, since comparisons have a reflection relationship instead of a commutative one, the `$left` argument is omitted as it doesn't make sense. They can also still throw exceptions, including the `InvalidOperator` exception.

```php
<?php

interface Equatable {

  public function __equals(never $other): bool;

}
```

The spaceship operator (`<=>`), used to determine sort hierarchy and encompassing all comparisons for numeric values, is also supported. However, its reflection and definition is slightly different:

```php
<?php

interface Comparable {

  public function __compareTo(never $other): int;

}
```

| Left Operand Method | Right Operand Method |
| ------------------- | -------------------- |
| `__compareTo()` | `__compareTo() * -1` |

Any return value larged than 0 will be normalized to 1, and any return value smaller than 0 will be normalized to -1.

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
| `==` | `__equals()` |
| `<=>` | `__compareTo()` |

**Implied Operators**

The following operators are supported due to optimizations and substitutions that occur within the PHP engine.

| Operator | Implied As | Method |
| -------- | ---------- | ------ |
| `$a += $b` | `$a = $a + $b` | `__add()` |
| `$a -= $b` | `$a = $a - $b` | `__sub()` |
| `$a *= $b` | `$a = $a * $b` | `__mul()` |
| `$a /= $b` | `$a = $a / $b` | `__div()` |
| `$a %= $b` | `$a = $a % $b` | `__mod()` |
| `$a **= $b` | `$a = $a ** $b` | `__pow()` |
| `$a != $b` | `!($a == $b)` | `__equals()` |
| `$a < $b` | `($a <=> $b) == -1` | `__compareTo()` |
| `$a <= $b` | `($a <=> $b) < 1` | `__compareTo()` |
| `$a > $b` | `($a <=> $b) == 1` | `__compareTo()` |
| `$a >= $b` | `($a <=> $b) > -1` | `__compareTo()` |
| `++$a` | `$a = $a + 1` | `__add()` |
| `$a++` | `$a = $a + 1` | `__add()` |
| `--$a` | `$a = $a - 1` | `__sub()` |
| `$a--` | `$a = $a - 1` | `__sub()` |
| `-$a` | `$a = -1 * $a` | `__mul()` |

### Interfaces

The following interfaces will be added:

| Interface | Method Signature |
| --------- | ---------------- |
| Addable | `__add(never $other, bool $left): mixed` |
| Subtractable | `__sub(never $other, bool $left): mixed` |
| Multipliable | `__mul(never $other, bool $left): mixed` |
| Dividable | `__div(never $other, bool $left): mixed` |
| Modable | `__mod(never $other, bool $left): mixed` |
| Powable | `__pow(never $other, bool $left): mixed` |
| Equatable | `__equals(never $other): bool` |
| Comparable | `__compareTo(never $other): int` |

The implementation checks for the interface on the class, so classes which implement the override method without declaring that they implement the interface will result in an `InvalidOperator` exception being thrown when used with the operator in question. This ensures the advantages described in this RFC which type variance and forced typing of the arguments provides.

## Backward Incompatible Changes

Objects used with operators will now error if the operator method is not implemented on the object or objects in question. Since expressions involving objects and these operators prior have been mostly nonsensical before, the anticipated impact of this is minimal.

## Proposed PHP Version

This change is proposed for PHP 8.2

## RFC Impact

### To SAPIs

None

### To Existing Extensions

Existing extensions can continue to define their own operator overloads by providing a `do_operation` call for their classes, however classes which are open to be extended may benefit from being updated so that their overloads can be extended by implementing the necessary methods.

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

### Implied Operators

Several operators are supported by this RFC through automatic optimizations that occur. Some of these operators could be supported with direct overrides, such as the implied comparison operators. Such direct support for implied operators is left to future scope.

### Scalar Objects

This RFC could impact and make the often explored scalar objects concept more fully featured, or even unneeded. It could, alternatively, make ensuring their behavior more difficult. Either way it is likely that this RFC would affect the scope of any scalar objects RFC.

### Exposing Core Overloads

As mentioned in this RFC, there are some objects within core that implement their own limited operator overloads. Deciding whether to update these objects and open their overloads for extension is left as future scope.

### Arbitrary Infixes

This RFC does not support R-style operator overloading, which allows users to define custom operators outside the symbol set supported by core. Such a feature would be part of a separate RFC.

## Proposed Voting Choices

Add limited user-defined operator overloads as described: yes/no. A 2/3 vote is required to pass. 

## Vote

## Patches and Tests

## References

[1]: https://en.wikipedia.org/wiki/Matrix_multiplication#Non-commutativity

## Changelog
