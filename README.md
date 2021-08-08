# PHP RFC: Operator Overloading

## Introduction

This RFC aims to provide basic operator overloading within PHP for objects.

## Background

Operator overloading is a well explored concept in programming that enables a programmer to control the behavior of their code when combined with an infix, or operator. Some languages such as R allow for defining custom operators in addition to overloading existing operators. Some languages such as Python allow for overloading all existing operators, but not for defining new operators. Some languages such as Java do not allow for custom operator overloading at all.

### Existing Operators in PHP

**Mathematical Operators**

| Symbol | Op Code | Description |
| **+** | ZEND_ADD | Used for addition with int and float, union with arrays |
| **-** | ZEND_SUB | Used for subtraction with int and float |
| **\*** | ZEND_MUL | Used for multiplication with int and float |
| **/** | ZEND_DIV | Used for division with int and float |
| **%** | ZEND_MOD | Used for modulo with int |
| **\*\*** | ZEND_POW | Used for pow with int |

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
