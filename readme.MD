Correlator
==========
v0.1 - early alpha version; rewriting from aged codebase.

Introduction
------------
Primarily intended to find the most influential factor in determining the
success of multiple measurable events.

Assumes the array set when construction contains the "scores" (reflecting the
relative success of something expressed as an interval or ratio set), whereas
the array compared against contains the "influence factor".
The method chose depends on the characteristics of influence factor.

Installation
------------

* Requires PHP 5.3 or above.
* No dependencies.
* Just `require` and use.

Usage example
-------------

	require_once 'Correlator.php';
	$scores = [1, 2, 3, 4, 5];
	$factor = [1, 2, 9, 4, 4];

	$Correlator = new Correlator/Correlator($scores);
	$rho = $Correlator->getPearsonsRPopulation($factor);

	echo $rho;

Further reading
---------------

* A more in-depth explaination of the types of data used:
  http://changingminds.org/explanations/research/measurement/types_data.htm

Future directions
-----------------

* Utils unittest.
* Per-method unittests?
* Performance comparison suite.
  * Profile
  * Improve performance
  * Measure relative complexity-> Relative big-O?
* "wizard" method taking self::CONSTANTS to describe the data.
* Implement more correlation methods.
  * Ficher-Exact
  * Non-parametric tests:
    * Kruskal-Wallis: http://www.mathcracker.com/kruskal-wallis.php
    * Wilcoxon Signed Ranks test: http://www.mathcracker.com/wilcoxon-signed-ranks.php
    * Wilcoxon Rank-Sum test: http://www.mathcracker.com/wilcoxon-rank-sum.php
    * Sign test: http://www.mathcracker.com/sign-test.php
* Check and improve accuracy for rounding errors in floats/doubles.
  * Check for really big numbers
* P-value calculation (for normalization). http://www.socscistatistics.com/pvalues/Default.aspx