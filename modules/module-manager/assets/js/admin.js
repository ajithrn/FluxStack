/**
 * FluxStack Module Manager Admin Scripts
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Handle reset to defaults button
        $('.fluxstack-settings-actions .button-secondary').on('click', function(e) {
            if (!confirm('Are you sure you want to reset all settings to their defaults?')) {
                e.preventDefault();
            }
        });
        
        // Handle module dependencies
        function handleModuleDependencies() {
            // For each module checkbox
            $('.module-checkbox').each(function() {
                var $checkbox = $(this);
                var moduleId = $checkbox.data('module');
                
                // If being unchecked
                if (!$checkbox.is(':checked')) {
                    // Find modules that depend on this one
                    var dependents = [];
                    
                    // Check each module to see if it depends on this one
                    $('.module-checkbox').each(function() {
                        var $dependent = $(this);
                        var dependsOn = $dependent.data('depends-on');
                        
                        if (dependsOn && dependsOn.split(',').indexOf(moduleId) !== -1) {
                            dependents.push($dependent);
                        }
                    });
                    
                    if (dependents.length > 0) {
                        // Disable dependent modules
                        $.each(dependents, function(i, $dependent) {
                            $dependent.prop('disabled', true).prop('checked', false);
                            $dependent.closest('tr').addClass('disabled-by-dependency');
                        });
                        
                        // Show warning
                        $('.dependency-warning[data-for="' + moduleId + '"]').show();
                    }
                } else {
                    // If being checked, re-enable modules that depend only on this
                    $('.module-checkbox').each(function() {
                        var $dependent = $(this);
                        var dependsOn = $dependent.data('depends-on');
                        
                        if (dependsOn) {
                            var dependencies = dependsOn.split(',');
                            
                            // Check if all dependencies are now enabled
                            var allEnabled = true;
                            $.each(dependencies, function(i, dep) {
                                if (!$('.module-checkbox[data-module="' + dep + '"]').is(':checked')) {
                                    allEnabled = false;
                                    return false; // break
                                }
                            });
                            
                            // If all dependencies are enabled, re-enable this module
                            if (allEnabled) {
                                $dependent.prop('disabled', false);
                                $dependent.closest('tr').removeClass('disabled-by-dependency');
                            }
                        }
                    });
                    
                    // Hide warning
                    $('.dependency-warning[data-for="' + moduleId + '"]').hide();
                }
            });
        }
        
        // Handle block dependencies
        function handleBlockDependencies() {
            // For each block checkbox
            $('.block-checkbox').each(function() {
                var $checkbox = $(this);
                var blockId = $checkbox.data('block');
                
                // If being unchecked
                if (!$checkbox.is(':checked')) {
                    // Find blocks that depend on this one
                    var dependents = [];
                    
                    // Check each block to see if it depends on this one
                    $('.block-checkbox').each(function() {
                        var $dependent = $(this);
                        var dependsOn = $dependent.data('depends-on-blocks');
                        
                        if (dependsOn && dependsOn.split(',').indexOf(blockId) !== -1) {
                            dependents.push($dependent);
                        }
                    });
                    
                    if (dependents.length > 0) {
                        // Disable dependent blocks
                        $.each(dependents, function(i, $dependent) {
                            $dependent.prop('disabled', true).prop('checked', false);
                            $dependent.closest('tr').addClass('disabled-by-dependency');
                        });
                        
                        // Show warning
                        $('.dependency-warning[data-for="' + blockId + '"]').show();
                    }
                } else {
                    // If being checked, re-enable blocks that depend only on this
                    $('.block-checkbox').each(function() {
                        var $dependent = $(this);
                        var dependsOn = $dependent.data('depends-on-blocks');
                        
                        if (dependsOn) {
                            var dependencies = dependsOn.split(',');
                            
                            // Check if all dependencies are now enabled
                            var allEnabled = true;
                            $.each(dependencies, function(i, dep) {
                                if (!$('.block-checkbox[data-block="' + dep + '"]').is(':checked')) {
                                    allEnabled = false;
                                    return false; // break
                                }
                            });
                            
                            // If all dependencies are enabled, re-enable this block
                            if (allEnabled) {
                                $dependent.prop('disabled', false);
                                $dependent.closest('tr').removeClass('disabled-by-dependency');
                            }
                        }
                    });
                    
                    // Hide warning
                    $('.dependency-warning[data-for="' + blockId + '"]').hide();
                }
            });
        }
        
        // Run on page load
        handleModuleDependencies();
        handleBlockDependencies();
        
        // Run when checkboxes change
        $('.module-checkbox').on('change', handleModuleDependencies);
        $('.block-checkbox').on('change', handleBlockDependencies);
        
        // Add tooltips to dependency badges
        $('.dependency-badge').on('mouseover', function() {
            var $this = $(this);
            var $row = $this.closest('tr');
            var itemName = $row.find('label').first().text().trim();
            
            // Show tooltip with list of dependent items
            var dependents = [];
            
            // Check for module dependencies
            $('.module-requires-notice').each(function() {
                var $notice = $(this);
                if ($notice.text().indexOf(itemName) !== -1) {
                    dependents.push($notice.closest('tr').find('label').first().text().trim());
                }
            });
            
            // Check for block dependencies
            $('.block-requires-notice').each(function() {
                var $notice = $(this);
                if ($notice.text().indexOf(itemName) !== -1) {
                    dependents.push($notice.closest('tr').find('label').first().text().trim());
                }
            });
            
            if (dependents.length > 0) {
                var tooltip = 'Required by: ' + dependents.join(', ');
                $this.attr('title', tooltip);
            }
        });
    });
})(jQuery);
