    通过代码的抽取，我们可以将原本的 MVC 设计模式中的 ViewController 进一步拆分，构造出网络请求层、ViewModel 层、Service 层、Storage 层等其它类，来配合 Controller 工作，从而使 Controller 更加简单，我们的 App 更容易维护。

　　另外，不知道大家注意到没，其实 Controller 层是非常难于测试的，如果我们能够将 Controller 瘦身，就可以更方便地写 Unit Test 来测试各种与界面的无关的逻辑。移动端自动化测试框架都不太成熟，但是将 Controller 的代码抽取出来，是有助于我们做测试工作的。