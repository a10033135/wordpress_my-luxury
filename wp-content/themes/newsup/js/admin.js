jQuery(function ($) {

    $(document).on("click", ".btn-install, .btn-activate", function (e) {
        e.preventDefault();

        let btn    = $(this);
        let box    = btn.closest(".bottom-item");
        let slug   = box.data("slug");     // plugin slug
        let plugin = box.data("plugin");   // plugin main file path

        // Redirect URLs
        let redirectURL = "";
        if (slug === "ansar-import") {
            redirectURL = "admin.php?page=ansar-demo-import";
        }
        if (slug === "blognews-for-elementor") {
            redirectURL = "admin.php?page=blognews_admin_menu";
        }

        /* -----------------------------------------------------------
         * CASE 1: PLUGIN ALREADY INSTALLED → ONLY ACTIVATE
         * ----------------------------------------------------------- */
        if (btn.hasClass("btn-activate")) {

            btn.html("Activating…").prop("disabled", true);

            $.ajax({
                url: newsup_ajax_obj.ajax_url,
                type: "POST",
                data: {
                    action: "newsup_activate_plugin",
                    nonce: newsup_ajax_obj.nonce,
                    plugin_file: plugin
                },
                success: function (response) {
                    
                    if (!response.success) {
                        btn.text("Failed").prop("disabled", false);
                        return;
                    }

                    btn.removeClass("btn-activate")
                       .addClass("btn-disabled")
                       .text("Activated");

                    // Redirect after activation
                    if (redirectURL !== "") {
                        setTimeout(() => window.location.href = redirectURL, 800);
                    }
                },
                error: function () {
                    btn.text("Error").prop("disabled", false);
                }
            });

            return; // Stop here (no install)
        }

        /* -----------------------------------------------------------
         * CASE 2: PLUGIN NOT INSTALLED → INSTALL + ACTIVATE
         * ----------------------------------------------------------- */
        if (btn.hasClass("btn-install")) {

            btn.html("Installing…").prop("disabled", true);

            // STEP 1 → INSTALL
            $.ajax({
                url: newsup_ajax_obj.ajax_url,
                type: "POST",
                data: {
                    action: "newsup_install_plugin",
                    nonce: newsup_ajax_obj.nonce,
                    slug: slug
                },
                success: function (response) {

                    if (!response.success) {
                        btn.text("Install Failed").prop("disabled", false);
                        return;
                    }

                    // STEP 2 → ACTIVATE
                    btn.html("Activating…");

                    $.ajax({
                        url: newsup_ajax_obj.ajax_url,
                        type: "POST",
                        data: {
                            action: "newsup_activate_plugin",
                            nonce: newsup_ajax_obj.nonce,
                            plugin_file: plugin
                        },
                        success: function (result) {

                            if (!result.success) {
                                btn.text("Activation Failed").prop("disabled", false);
                                return;
                            }

                            btn.removeClass("btn-install")
                               .addClass("btn-disabled")
                               .text("Activated");

                            // Redirect
                            if (redirectURL !== "") {
                                setTimeout(() => window.location.href = redirectURL, 800);
                            }
                        },
                        error: function () {
                            btn.text("Error").prop("disabled", false);
                        }
                    });
                },
                error: function () {
                    btn.text("Error").prop("disabled", false);
                }
            });
        }

    });
    $('#newsup-upgrade-menu-item').parent().attr('target', '_blank');
});

