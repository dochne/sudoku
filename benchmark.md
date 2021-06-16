# Benchmarks
|Language|ImplementationName|Average|Example 1|Example 2|Example 3|Example 4|Example 5|
|---|---|---|---|---|---|---|---|
|rust|optimised-multithreaded|0.010311|0.010311|0.009994|0.009978|0.011142|0.010128|
|rust|optimised|0.013079|0.008879|0.010044|0.009187|0.013491|0.023794|
|rust|basic|0.052985|0.010205|0.009934|0.009645|0.225037|0.010105|
|php|bitwise-pcntl|0.059766|0.050221|0.048242|0.048398|0.098236|0.053733|
|php|bruteforce-linked-flexible-distribution-bitwise|0.085772|0.041545|0.040301|0.03962|0.253147|0.054248|
|go|basic|0.142242|0.014514|0.014658|0.01497|0.652728|0.014338|
|php|bruteforce-linked-flexible-distribution-pcntl|0.189029|0.045644|0.047339|0.045886|0.744224|0.062052|
|typescript|bruteforce|0.197236|0.047046|0.048717|0.05058|0.788458|0.051378|
|php|andy-hacky-solver-v2|0.286814|0.039039|0.038816|0.04189|0.419466|0.89486|
|php|andy-hacky-solver|0.34887|0.044172|0.0402|0.041676|0.447848|1.170456|
|php|bruteforce-linked-flexible-distribution-goto|0.450585|0.036479|0.040275|0.037862|2.017827|0.12048|
|php|bruteforce-linked-flexible-distribution|0.458143|0.041925|0.040349|0.03866|2.049708|0.120074|
|php|bruteforce-linked|0.555406|0.041217|0.040776|0.038703|2.615241|0.041094|
|php|andy-recursive-solver-with-optimisation|1.1982|0.039347|0.044389|0.049215|1.752789|4.105262|
|php|pseudo-dancinglinks-immutable|1.770398|0.040835|0.043088|0.045008|8.672215|0.050844|
|php|bruteforce|2.428485|0.040115|0.044429|0.046928|11.957073|0.05388|
|php|bruteforce-immutable|2.478972|0.041245|0.044329|0.046829|12.205619|0.05684|
|python|bruteforce|5.519506|0.075228|0.052936|0.053432|27.34979|0.066142|
|php|bruteforce-linked-flexible-start|6.008123|0.03899|0.046924|0.269172|0.465215|29.220312|
|php|andy-tweeked-solver|6.363722|0.041412|0.04045|0.052583|17.497969|14.186197|
|php|andy-recursive-solver|10.455866|0.047312|0.045452|0.215386|15.933791|36.037386|

# Self Reported Benchmarks
|Language|ImplementationName|Average|Example 1|Example 2|Example 3|Example 4|Example 5|
|---|---|---|---|---|---|---|---|
|php|bitwise-pcntl|[32m0.01982[0m|0.009155|0.008107|0.007769|[32m0.058322[0m|0.015746|
|php|bruteforce-linked-flexible-distribution-bitwise|0.044964|[32m0.000253[0m|[32m0.000422[0m|[32m0.000144[0m|0.215282|0.00872|
|php|bruteforce-linked-flexible-distribution-pcntl|0.151158|0.00732|0.009648|0.007897|0.707935|0.02299|
|php|andy-hacky-solver-v2|0.247604|0.000406|0.001091|0.002368|0.381106|0.853047|
|php|andy-hacky-solver|0.309826|0.000671|0.001739|0.003437|0.410134|1.133149|
|php|bruteforce-linked-flexible-distribution-goto|0.413411|0.000525|0.00239|0.00059|1.979928|0.083623|
|php|bruteforce-linked-flexible-distribution|0.419172|0.000515|0.002428|0.000604|2.011246|0.081065|
|php|bruteforce-linked|0.516858|0.001029|0.002615|0.001496|2.577603|[32m0.001545[0m|
|php|andy-recursive-solver-with-optimisation|1.160125|0.001475|0.005158|0.011271|1.715656|4.067067|
|php|pseudo-dancinglinks-immutable|1.731841|0.00218|0.004591|0.006393|8.634976|0.011066|
|php|bruteforce|2.390569|0.002369|0.006829|0.008679|11.920391|0.014579|
|php|bruteforce-immutable|2.440018|0.002307|0.007002|0.008851|12.166848|0.015081|
|php|bruteforce-linked-flexible-start|5.969643|0.000578|0.008583|0.231294|0.427298|29.180461|
|php|andy-tweeked-solver|6.325805|0.002611|0.003101|0.014978|17.460721|14.147613|
|php|andy-recursive-solver|10.416803|0.002727|0.006743|0.177876|15.896452|36.000219|
