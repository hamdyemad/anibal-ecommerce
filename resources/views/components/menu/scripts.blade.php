                const parentUl = activeItem.closest('ul');
                if (parentUl) {
                    const parentHasChild = parentUl.closest('li.has-child');
                    if (parentHasChild) {
                        // Add open class to parent menu
                        parentHasChild.classList.add('open');

                        // Make sure parent link is also active
                        const parentLink = parentHasChild.querySelector(':scope > a');
                        if (parentLink && !parentLink.classList.contains('active')) {
                            parentLink.classList.add('active');
                        }

                        // Show the submenu
                        const submenu = parentHasChild.querySelector(':scope > ul');
                        if (submenu) {
                            submenu.style.display = 'block';
                        }
                    }
                }
            });

            // Also handle menus that already have the 'open' class from PHP
            const openMenus = document.querySelectorAll('.sidebar_nav li.has-child.open');
            openMenus.forEach(function(menu) {
                const parentLink = menu.querySelector(':scope > a');
                if (parentLink && !parentLink.classList.contains('active')) {
                    parentLink.classList.add('active');
                }

                // Ensure submenu is visible
                const submenu = menu.querySelector(':scope > ul');
                if (submenu) {
                    submenu.style.display = 'block';
                }
            });
        }

        // Run the function
        openParentMenus();

        // Also run after a short delay to ensure all elements are rendered
        setTimeout(openParentMenus, 100);
    });
</script>
