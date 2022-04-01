$(document).ready(function() {
 init();
  $('#add_all_feature_button').on('click', (e) => {
    e.preventDefault();
    add();

  });

  function add() {

    const collectionHolderMain = $('.feature-collection');
    const collectionHolder = $('.all-feature-collection');
    let formCollection = collectionHolder.attr('data-prototype');

    const newForm = formCollection;
    collectionHolderMain.append(newForm);
    cleanForm();
    setTimeout(
      function()
      {
        // by default form.js prestashop add always default row // temp solution
        $('.product-feature').slice(-1).remove();
      }, 1000);

  }

  function init()
  {
    // show btn closely to add feature until prestashop add a hook that allow as to display the btn beside the main
    let mainBtn = $('#add_feature_button').closest('.row');
    $("#btn_add_all_features").detach().appendTo(mainBtn);
  }

  function cleanForm()
  {
    let allFeatures = $('.feature-collection').children('.row').length
    let indexStart = indexStartValue = indexStartCustom = (allFeatures - $("[name='product_feature[feature]']").length)

    $("[name='product_feature[feature]']").each(function() {

      $(this).attr('name','form[step1][features]['+indexStart+'][feature]')
      indexStart++

    });

    $("[name='product_feature[value]").each(function() {

      $(this).attr('name','form[step1][features]['+indexStartValue+'][value]')
      indexStartValue++

    });

    $("[name='product_feature[custom_value][1]").each(function() {

      $(this).attr('name','form[step1][features]['+indexStartCustom+'][custom_value][1]')
      indexStartCustom++

    });
  }
});
