<?php

$playersQueryBuilder = ;
$rule  = 'gender = "F"';

$updatedQueryBuilder = $rulerz->applyFilter($playersQueryBuilder, $rule);

$results = $updatedQueryBuilder->getQuery()->getResult('CustomHydrator');
