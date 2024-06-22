//Wizard Init
var kycForm = $("#wizard");
$("#wizard").steps({
    headerTag: "h3",
    bodyTag: "section",
    transitionEffect: "none",
    titleTemplate: '<span class="number">#index#</span><span class="bd-wizard-step-title">#title#</span>',
    onStepChanging: function (event, currentIndex, newIndex)
    {
      kycForm.validate().settings.ignore = ":disabled,:hidden";
      return kycForm.valid();
    },
    onFinishing: function (event, currentIndex)
    {
      kycForm.validate().settings.ignore = ":disabled";
      return kycForm.valid();
    },
    onFinished: function(event, currentIndex) {
    //   alert("Submitted!");
      $(kycForm).submit();
    }
});