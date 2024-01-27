const logout_submit = document.getElementById("logout_submit");

const logOut = () => {
  if (confirm("로그아웃 하시겠습니까?")) {
    logout_submit.type = "submit"; //true일 때 submit으로 넘김
  }
};

logout_submit.addEventListener("click", logOut);
