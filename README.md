# PHP RFC: Operator Overloading

## Introduction

This RFC aims to provide basic operator overloading within PHP for objects.

## Background

Operator overloading is a well explored concept in programming that enables a programmer to control the behavior of their code when combined with an infix, or operator. Some languages such as R allow for defining custom operators in addition to overloading existing operators. Some languages such as Python allow for overloading all existing operators, but not for defining new operators. Some languages such as Java do not allow for custom operator overloading at all.

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
| **<=** | Less than or equal to comarpison |
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

## Proposal

## Backward Incompatible Changes

## Proposed PHP Version

## RFC Impact

## Future Scope

## Proposed Voting Choices

## Vote

## Patches and Tests

## References

## Changelog
