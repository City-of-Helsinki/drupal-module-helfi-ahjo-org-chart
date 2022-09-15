(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.buildTree = {
    attach: function (context, settings) {
      // get color_body value with "drupalSettings.mymodule.color_body"
      //The structure of your tree


      //The structure of your tree
      const trees = drupalSettings.helfi_ahjo;
      var editForm = function () {
        return false;
      }
      editForm.prototype.init = function (obj) {
        return false
      }

      editForm.prototype.show = function (node) {
        return false;
      }

      editForm.prototype.hide = function (showldUpdateTheNode) {
        return false
      }
      OrgChart.templates.myTemplate = Object.assign({}, OrgChart.templates.ana);
      OrgChart.templates.myTemplate.field_0 = '<text data-width="230" data-text-overflow="multiline" style="font-size: 16px;" fill="#ffffff" x="125" y="30" text-anchor="middle">{val}</text>';
      let chart = new OrgChart(document.getElementById("tree"), {
        layout: OrgChart.mixed,
        template: "myTemplate",
        editUI: new editForm(),
        enableDragDrop: false,

        menu: {
          pdfPreview: {
            text: "PDF Preview",
            icon: OrgChart.icon.pdf(24,24, '#7A7A7A'),
            onClick: preview
          },
          pdf: { text: "Export PDF" },
          png: { text: "Export PNG" },
          svg: { text: "Export SVG" },
          csv: { text: "Export CSV" }
        },
        nodeMenu: {
          pdf: { text: "Export PDF" },
          png: { text: "Export PNG" },
          svg: { text: "Export SVG" }
        },
          nodeBinding: {
            field_0: "name"
          },
          nodes: trees

        });

      function preview(){
        OrgChart.pdfPrevUI.show(chart, {
          format: 'A4'
        });
      }
    }
  };
})(jQuery, Drupal, drupalSettings);
