function handleDateChange(dateInputId, passwordContainerId, className) {
    $("#" + dateInputId).change(function () {
        var selectedDate = new Date($(this).val()); // Get selected date
        var currentDate = new Date();
        currentDate.setHours(0, 0, 0, 0); // Normalize current date to remove time

        // Check if selected date is in the past
        if (selectedDate < currentDate) {
            $("#" + passwordContainerId).empty();

            // Create HTML elements dynamically using jQuery
            var passwordDiv = $("<div>").addClass(className);
            var formGroup = $("<div>").addClass("form-group row");
            var colDiv = $("<div>").addClass("col-sm-12");
            var title = $("<h4>").addClass("card-title").text("Password (when backdated entry needs password)");
            var passwordInput = $("<input>").attr({
                type: "password",
                id: "password",
                name: "password",
                autocomplete: "new-password"
            }).addClass("form-control").val("");
            var errorSpan = $("<span>").attr("id", "d_o_i_error").addClass("error").css("color", "red");

            // Append elements in the correct structure
            colDiv.append(title, passwordInput, errorSpan);
            formGroup.append(colDiv);
            passwordDiv.append(formGroup);

            // Append the final structure to the container
            $("#" + passwordContainerId).append(passwordDiv);
        } else {
            $("#" + passwordContainerId).empty();
        }
    });
}

function passwordValidate() {
    if ($("#password").length) {
        alert("11D1");
        var passwordValue = $("#password").val();

        if (passwordValue === "") {
            alert("Password is required!");
            return false; // Prevent further execution
        }
        if (passwordValue != "@#$5A&*Cr!F11111") {
            alert("password did not match!");
            return false;
        }
    }
}
