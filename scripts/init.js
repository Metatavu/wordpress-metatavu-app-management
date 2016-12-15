(function ($) {
  'use strict';

  function initializeFormComponents() {
    flatpickr('.metatavu-app-management-date.uninitialized', {
      dateFormat: 'j.n.Y'
    });
    $('.metatavu-app-management-time.uninitialized').timepicker({
      timeFormat: 'G:i'
    });
    $('.uninitialized').removeClass('uninitialized');
  }

  $(document).ready(function () {

    initializeFormComponents();

    $('.metatavu-app-management-add-row-btn').click(function (e) {
      e.preventDefault();
      $('.metatavu-app-management-open-container').append(METATAVU_APP_MANAGEMENT_OPEN_ROW);
      initializeFormComponents();
    });

    $(document).on('click', '.metatavu-app-management-remove-row-btn', function (e) {
      e.preventDefault();
      $(this).parents('p').remove();
    });

  });

})(jQuery);