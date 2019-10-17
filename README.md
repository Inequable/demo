采用PHP编写的代码


# IPCalculator.php 是网络和IP地址计算器
> 调取getIPInfo('192.168.1.53/27')方法，输入合理的IP段即可以输出，这个IP段下包括：ip，掩位吗，子网掩码等信息

# multi2point.php 是将多维数组转化成一维数组，键名由多维数组的键名由“.”拼接

```
调用方式：var_export($this->loopHH($dd))；
$dd = [                      [
     'a'=>[                      "a.b.0.0" => 1,
         'b'=>[                  "a.b.0.1" => 2,
             [1,2,3,4]  ==>      "a.b.0.2" => 3,
         ],             ==>      "a.b.0.3" => 4,
         'c'=>[         ==>      "a.c.0.0" => 2,
             [2,3,4]             "a.c.0.1" => 3,
         ]                       "a.c.0.2" => 4
     ],                      ]
 ];
```

采用python编写的代码

# firstScrapy.py 是python 爬虫应用
> 学习于 http://www.cnblogs.com/linhaifeng/articles/7773496.html 
> 需要自行创建一个保存视屏的目录文件，并修改firstScrapy.py文件中的mp4保存路径
