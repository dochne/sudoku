# Benchmarks
|Language|ImplementationName|Average|Example 1|Example 2|Example 3|Example 4|Example 5|
|---|---|---|---|---|---|---|---|
|rust|optimised|0.007665|0.006279|0.00495|0.004854|0.014253|0.007988|
|rust|basic|0.043353|0.004135|0.004684|0.004179|0.199632|0.004137|
|php|bitwise-pcntl|0.045297|0.033626|0.033742|0.033731|0.086941|0.038447|
|php|bruteforce-linked-flexible-distribution-bitwise|0.071935|0.025974|0.026725|0.027412|0.246|0.033562|
|php|bruteforce-linked-flexible-distribution-pcntl|0.193145|0.031416|0.035072|0.033964|0.815782|0.049491|
|typescript|bruteforce|0.197236|0.047046|0.048717|0.05058|0.788458|0.051378|
|go|bruteforce|0.21384|0.311|0.021423|0.005299|0.710735|0.020742|
|php|andy-hacky-solver-v2|0.300052|0.026667|0.028915|0.02929|0.439509|0.975877|
|php|andy-hacky-solver|0.353583|0.030782|0.029251|0.030481|0.466317|1.211084|
|php|bruteforce-linked-flexible-distribution|0.48205|0.030062|0.029145|0.02677|2.213253|0.11102|
|php|bruteforce-linked-flexible-distribution-goto|0.489471|0.025249|0.027662|0.026887|2.251762|0.115795|
|php|bruteforce-linked|0.56889|0.026927|0.029183|0.02748|2.735416|0.025442|
|php|andy-recursive-solver-with-optimisation|1.227091|0.027797|0.033474|0.039116|1.80184|4.233227|
|php|pseudo-dancinglinks-immutable|1.91314|0.029764|0.03115|0.033232|9.432602|0.038954|
|php|bruteforce-immutable|2.650075|0.028779|0.033745|0.035739|13.109351|0.042763|
|php|bruteforce|2.657645|0.036813|0.033887|0.035754|13.138212|0.04356|
|php|bruteforce-linked-flexible-start|6.491294|0.028354|0.035074|0.263099|0.486183|31.643759|
|php|andy-tweeked-solver|6.954395|0.027388|0.027901|0.042413|19.1724|15.501871|
|python|bruteforce|7.157704|0.063975|0.046812|0.047573|35.564706|0.065456|
|php|andy-recursive-solver|11.331384|0.032222|0.034206|0.215514|17.068852|39.306125|

# Self Reported Benchmarks
|Language|ImplementationName|Average|Example 1|Example 2|Example 3|Example 4|Example 5|
|---|---|---|---|---|---|---|---|
|php|bitwise-pcntl|[32m0.018796[0m|0.007878|0.006593|0.006421|[32m0.059376[0m|0.013712|
|php|bruteforce-linked-flexible-distribution-bitwise|0.045973|[32m0.00012[0m|[32m0.000395[0m|[32m0.000144[0m|0.220088|0.009118|
|php|bruteforce-linked-flexible-distribution-pcntl|0.167099|0.0066|0.008156|0.006757|0.789626|0.024355|
|php|andy-hacky-solver-v2|0.273621|0.000436|0.001209|0.002597|0.41349|0.950374|
|php|andy-hacky-solver|0.326734|0.000718|0.001819|0.00354|0.439685|1.187907|
|php|bruteforce-linked-flexible-distribution|0.455405|0.000429|0.002574|0.000636|2.187628|0.085757|
|php|bruteforce-linked-flexible-distribution-goto|0.464318|0.000469|0.002643|0.000626|2.226398|0.091452|
|php|bruteforce-linked|0.543321|0.00113|0.002747|0.001586|2.709844|[32m0.001298[0m|
|php|andy-recursive-solver-with-optimisation|1.200415|0.001408|0.005387|0.011698|1.777943|4.205637|
|php|pseudo-dancinglinks-immutable|1.886462|0.002513|0.004898|0.006902|9.406699|0.011296|
|php|bruteforce-immutable|2.623648|0.002909|0.007391|0.009267|13.083137|0.015534|
|php|bruteforce|2.629331|0.003324|0.007272|0.009226|13.112042|0.014793|
|php|bruteforce-linked-flexible-start|6.4654|0.000609|0.008802|0.237722|0.46101|31.618858|
|php|andy-tweeked-solver|6.928577|0.00256|0.003365|0.015993|19.146463|15.474505|
|php|andy-recursive-solver|11.303638|0.001947|0.006503|0.188365|17.043145|39.278228|
