<?php
function A () {
	return 'B';
}

function B() {
	return 'A';
}

$A = A();
echo $A();