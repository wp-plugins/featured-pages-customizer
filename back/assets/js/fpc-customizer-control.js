/*! Featured Pages Customizer controls by Nicolas Guillaume, GPL2+ licensed */
jQuery(function ($) {
  //prevents js conflicts
  "use strict";
  var $Selects   = $('select' , '#accordion-section-tc_fpc'),
      $Checks    = $('input[type=checkbox]' , '#accordion-section-tc_fpc');

  //init selecter
  $Selects.selecter();

  //init icheck only if not already initiated
  $Checks.each( function() {
    if ( 0 === $(this).closest('div[class^="icheckbox"]').length ) {
      $(this).iCheck({
          checkboxClass: 'icheckbox_flat-grey',
          radioClass: 'iradio_flat-grey'
      })
      .on('ifChanged', function(event) {
        $(this).trigger('change');
      });
    }
  });
  var api          = wp.customize,
      ShowFP       = TCFPCControlParams.ShowFP,
      ShowExcerpt  = TCFPCControlParams.ShowExcerpt,
      ShowButton   = TCFPCControlParams.ShowButton,
      OptionPrefix = TCFPCControlParams.OptionPrefix,
      settingMap   = {};

  settingMap[OptionPrefix + "[tc_show_fp]"] = {
      controls: ShowFP,
      callback: function( to ) { return 1 == to; }
  };
  settingMap[OptionPrefix + "[tc_show_fp_text]"] = {
      controls: ShowExcerpt,
      callback: function( to ) { return 1 == to; }
  };
  settingMap[OptionPrefix + "[tc_show_fp_button]"] = {
      controls: ShowButton,
      callback: function( to ) { return 1 == to; }
  };

  $.each(settingMap, function( settingId, o ) {
    api( settingId, function( setting ) {
      $.each( o.controls, function( i, controlId ) {
        api.control( controlId, function( control ) {
          var visibility = function( to ) {
            control.container.toggle( o.callback( to ) );
          };
          visibility( setting.get() );
          setting.bind( visibility );
        });
      });
    });
  });


  //In controls call to action
  _render_fpc_cta();
  function _render_fpc_cta() {
    if ( 'function' != typeof(_) )
      return;
    // Grab the HTML out of our template tag and pre-compile it.
    var _cta = _.template(
        $( "script#fpc_cta" ).html()
    );
    $('li[id*="tc_fpc_options-tc_fp_position"]').prepend( _cta() );
  }
} );