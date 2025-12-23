import UsersStored from "../store/UsersStored.js";
import { checkUsername, handleLogin } from "./data.js";

$(() => {
	// const name = $('#name').val();
	//   const nameRegex = /^[^~`!@#$%^&*()]*$/;
	//   if (!nameRegex.test(name)) {
	//     $('#nameError').html('Tên không được chứa ký tự đặc biệt');
	//     return false;
	//   } else $('#nameError').html('');

	function validate(regex, inputID, errorID, msg) {
		const input = $(inputID).val().trim();
		const regexObj = regex;
		if (!regexObj.test(input)) {
			$(errorID).html(msg);
			$(inputID).css("border", "2px solid #FF5722");
			return false;
		} else {
			$(errorID).html("");
			$(inputID).css("border", "2px solid #00E676");
			return true;
		}
	}

	function validateName() {
		return validate(
			/^[^~`!@#$%^&*()]{8,}$/,
			"#name",
			"#nameError",
			"Tên không chứa ký tự đặc biệt và tối thiểu 8 ký tự"
		);
	}

	function validateSDT() {
		return validate(
			/^0[1-9]\d{8}$/,
			"#sdt",
			"#sdtError",
			"Số điện thoại không đúng"
		);
	}

	async function validateUsername() {
		const input = $("#username").val();
		if (await checkUsername(input)) {
			$("#username").css("border", "2px solid #FF5722");
			$("#userError").html("Tên đăng nhập đã tồn tại");
			return false;
		}
		return validate(
			/^[a-zA-Z0-9]{5,20}$/,
			"#username",
			"#userError",
			"Tên tài khoản chỉ gồm các ký tự và số, từ 5 đến 20 ký tự"
		);
	}

	function validatePassword() {
		return validate(
			/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/,
			"#password",
			"#passError",
			"Mật khẩu phải gồm ít nhất 1 ký tự hoa, 1 ký số và 1 ký tự đặc biệt. Tối thiểu 8 ký tự"
		);
	}

	function validateRePassword() {
		if (
			$("#repassword").val() &&
			$("#password").val().trim() == $("#repassword").val().trim()
		) {
			$("#repassword").css("border", "2px solid #00E676");
			$("#repassError").html("");
			return true;
		} else {
			$("#repassError").html("Mật khẩu không khớp");
			$("#repassword").css("border", "2px solid #FF5722");
			return false;
		}
	}

	$("#name").on("blur", validateName);
	$("#sdt").on("blur", validateSDT);
	$("#username").on("blur", validateUsername);
	$("#password").on("blur", validatePassword);
	$("#repassword").on("blur", validateRePassword);

	$("#js-sign-btn").on("click",  async() => {
		if (
			validateName() &&
			validateSDT() &&
			(await validateUsername()) &&
			validatePassword() &&
			validateRePassword()
		) {
			const newAccount = {
				username: $("#username").val().trim(),
				password: $("#password").val().trim(),
				name: $("#name").val().trim(),
				sdt: $("#sdt").val().trim(),
			};

			const localUsers = new UsersStored();
			localUsers.addUser(newAccount);
			localUsers.saveToLocalStorage();

			alter("Chúc mừng bạn đã đăng ký thành công!!");
			window.location.href = "../vegetable/Dangnhap.html";

			//handleLogin(newAccount.username, newAccount.password);
		}
	});
	/*function handleRegister() {
		// Giả sử bạn đã kiểm tra hợp lệ form ở đây và lưu thông tin người dùng thành công
	
		// Hiển thị thông báo đăng ký thành công
		alter("Chúc mừng bạn đã đăng ký thành công!!");
	
		// Chuyển hướng đến trang đăng nhập
		window.location.href = "../vegetable/Dangnhap.html";
	}
	
	// Gọi hàm xử lý đăng ký khi người dùng nhấn nút "Đăng ký"
	document.querySelector("#js-sign-btn").addEventListener("click", handleRegister);*/
	
});
