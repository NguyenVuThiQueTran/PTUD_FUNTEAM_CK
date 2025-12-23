import { handleLogin } from "./data.js";

$(() => {
	async function login() {
		const username = $("#username").val().trim();
		const password = $("#password").val().trim();

		if (!username) {
			$("#userError").html("Tài khoản không được rỗng");
			return;
		} else $("#userError").html("");

		if (!password) {
			$("#passError").html("Mật khẩu không được rỗng");
			return;
		} else $("#passError").html("");

		const isCorrect = await handleLogin(username, password);
		if (!isCorrect) {
			$("#passError").html("Tài khoản hoặc mật khẩu không chính xác");
		}
	}

	$("#js-login-btn").on("click", login);
	window.addEventListener("keydown", (e) => {
		if (e.key === "Enter") login();
	});
});
