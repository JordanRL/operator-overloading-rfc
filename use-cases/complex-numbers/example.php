<?php

function assert($descr,$iftrue) {
    printf("%s -> %s\n", $descr, $iftrue ? 'passed' : 'failed' );
}

use Examples\ComplexNumbers\ComplexNumber;
use Examples\ComplexNumbers\Real;
use Examples\ComplexNumbers\Imaginary;

$c1 = new ComplexNumber(new Real(5),new Imaginary(4));

$c2 = new ComplexNumber(new Real(0),new Imaginary(7));

$c3 = new ComplexNumber(new Real(3),new Imaginary(0));

$r1 = $c1 + $c2;
$r2 = $c1 + $c3;
$r3 = $c2 + $c3;

assert('$c1 + $c2: real', 5 === $r1->getRealPart());
assert('$c1 + $c3: real', 8 === $r2->getRealPart());
assert('$c2 + $c3: real', 3 === $r3->getRealPart());
assert('$c1 + $c2: imaginary', 11 === $r1->getImaginaryPart());
assert('$c1 + $c3: imaginary',  4 === $r2->getImaginaryPart());
assert('$c2 + $c3: imaginary',  7 === $r3->getImaginaryPart());

$r1 = $c1 * $c2;
$r2 = $c1 * $c3;
$r3 = $c2 * $c3;

/**
 * @var ComplexNumber $r1,$r2,$r3
 */
assert('$c1 * $c2: real', 28 === $r1->getRealPart());
assert('$c1 * $c3: real', 15 === $r2->getRealPart());
assert('$c2 * $c3: real',  0 === $r3->getRealPart());
assert('$c1 * $c2: imaginary', 35 === $r1->getImaginaryPart());
assert('$c1 * $c3: imaginary', 12 === $r2->getImaginaryPart());
assert('$c2 * $c3: imaginary', 21 === $r3->getImaginaryPart());

