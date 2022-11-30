# Benchmarks
|Language|ImplementationName|Average|Example 1|Example 2|Example 3|Example 4|Example 5|
|---|---|---|---|---|---|---|---|
|rust|optimised|[32m0.007494[0m|0.011796|0.005725|0.00536|[32m0.008442[0m|0.006148|
|rust|optimised-multithreaded|0.008269|0.010812|[32m0.005416[0m|0.006533|0.011904|0.00668|
|rust|basic|0.049968|[32m0.008224[0m|0.005937|[32m0.005298[0m|0.225028|[32m0.005354[0m|
|php|bitwise-pcntl|0.056378|0.044617|0.04496|0.045735|0.094577|0.051999|
|php|bruteforce-linked-flexible-distribution-bitwise|0.08006|0.035603|0.039656|0.039718|0.24181|0.043512|
|go|basic|0.143468|0.021561|0.015642|0.013145|0.658437|0.008556|
|typescript|bruteforce|0.186907|0.050954|0.053106|0.053963|0.722232|0.054281|
|php|bruteforce-linked-flexible-distribution-pcntl|0.199843|0.044465|0.046414|0.045101|0.804622|0.058612|
|php|andy-hacky-solver-v2|0.287826|0.036159|0.040819|0.041834|0.416609|0.903708|
|php|andy-hacky-solver|0.343495|0.039313|0.040562|0.043186|0.44944|1.144974|
|php|bruteforce-linked-flexible-distribution|0.471594|0.03464|0.039942|0.040375|2.124396|0.118618|
|php|bruteforce-linked-flexible-distribution-goto|0.477577|0.035947|0.040635|0.039851|2.151496|0.119958|
|php|bruteforce-linked|✘|✘|✘|✘|✘|✘|
|php|andy-recursive-solver-with-optimisation|1.15214|0.03731|0.042723|0.04965|1.691559|3.939459|
|php|pseudo-dancinglinks-immutable|1.689861|0.039546|0.042701|0.045164|8.275986|0.045909|
|php|bruteforce-immutable|2.395062|0.04181|0.04567|0.046571|11.792832|0.048425|
|php|bruteforce|✘|✘|✘|✘|✘|✘|
|ruby|ref|4.832387|0.092489|0.090664|0.095753|23.700006|0.183022|
|ruby|better_version|4.876602|0.065594|0.075762|0.080139|24.074691|0.086822|
|ruby|basic|5.739497|0.069855|0.078399|0.08063|28.372309|0.096292|
|python|bruteforce|5.911602|0.098192|0.048657|0.052552|29.293153|0.065458|
|php|andy-tweeked-solver|6.064581|0.036528|0.041557|0.052489|16.681839|13.510492|
|php|bruteforce-linked-flexible-start|6.30651|0.038152|0.04739|0.265116|0.484481|30.697409|
|php|andy-recursive-solver|10.18824|0.037975|0.043065|0.206917|15.510758|35.142485|

# Self Reported Benchmarks
|Language|ImplementationName|Average|Example 1|Example 2|Example 3|Example 4|Example 5|
|---|---|---|---|---|---|---|---|
|php|bitwise-pcntl|[32m0.0168[0m|0.004805|0.005065|0.005003|[32m0.054702[0m|0.014425|
|php|bruteforce-linked-flexible-distribution-bitwise|0.042014|[32m0.000107[0m|[32m0.000355[0m|[32m0.000134[0m|0.202371|[32m0.007103[0m|
|php|bruteforce-linked-flexible-distribution-pcntl|0.160482|0.004809|0.006716|0.005143|0.764031|0.021711|
|php|andy-hacky-solver-v2|0.249229|0.000317|0.001056|0.002267|0.376345|0.866162|
|php|andy-hacky-solver|0.304908|0.000515|0.001701|0.003283|0.409019|1.110023|
|php|bruteforce-linked-flexible-distribution|0.433945|0.000416|0.002258|0.0006|2.085331|0.081119|
|php|bruteforce-linked-flexible-distribution-goto|0.439926|0.000413|0.002457|0.000591|2.113845|0.082326|
|php|andy-recursive-solver-with-optimisation|1.114184|0.001103|0.004748|0.010324|1.652819|3.901926|
|php|pseudo-dancinglinks-immutable|1.651481|0.001607|0.004209|0.005885|8.237223|0.008482|
|php|bruteforce-immutable|2.357493|0.002136|0.006333|0.007958|11.757935|0.013104|
|php|andy-tweeked-solver|6.026827|0.002166|0.002851|0.013566|16.642666|13.472886|
|php|bruteforce-linked-flexible-start|6.269298|0.00052|0.008275|0.22776|0.448486|30.661448|
|php|andy-recursive-solver|10.151143|0.002126|0.005327|0.167351|15.475268|35.105644|
