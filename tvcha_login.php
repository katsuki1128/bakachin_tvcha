<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>tvchaログイン画面</title>
  <script src="https://cdn.tailwindcss.com"></script>

</head>

<body>
  <section class="bg-gray-50">
    <form action="tvcha_login_act.php" method="POST">
      <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
        <div class="w-full bg-white rounded-lg shadow sm:max-w-md xl:p-0">
          <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
            <div class="flex items-center justify-center">
              <img class="w-80 h-30 mr-2" src="./img/tvcha_logo.png" alt="logo">
            </div>
            <form class="space-y-4 md:space-y-6" action="#">
              <div>
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900">ユーザー名</label>
                <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" placeholder="your id" required="">
              </div>
              <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">パスワード</label>
                <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" required="">
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input id="remember" aria-describedby="remember" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300" required="">
                  </div>
                  <div class="ml-3 text-sm">
                    <label for="remember" class="text-gray-500">Remember me</label>
                  </div>
                </div>
              </div>
              <button type="submit" class="w-full text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">ログイン</button>

            </form>
          </div>
        </div>
      </div>
    </form>
  </section>


</body>

</html>