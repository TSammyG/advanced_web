$(document).ready(function () {
  // On clicking the signup, hide the login and show the registration form
  $("#signup").click(function () {
    $("#first").slideUp("slow", function () {
      $("#second").slideDown("slow");
    });
  });

  // On clicking the signup, hide the registration and show the login form
  $("#signin").click(function () {
    $("#second").slideUp("slow", function () {
      $("#first").slideDown("slow");
    });
  });
});
