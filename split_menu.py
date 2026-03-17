#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import re

# Read the menu file
with open('resources/views/partials/_menu.blade.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Define sections with their start and end markers
sections = {
    'dashboard': {'start': 225, 'end': 235},
    'withdraw': {'start': 237, 'end': 355},
    'push-notifications': {'start': 356, 'end': 392},
    'request-quotations': {'start': 393, 'end': 429},
    'accounting': {'start': 430, 'end': 471},
    'catalog': {'start': 472, 'end': 528},
    'products': {'start': 529, 'end': 750},
    'bundles': {'start': 751, 'end': 820},
    'taxes': {'start': 821, 'end': 835},
    'occasions': {'start': 836, 'end': 855},
    'reviews': {'start': 856, 'end': 895},
    'brands': {'start': 896, 'end': 910},
    'promocodes': {'start': 911, 'end': 925},
    'points': {'start': 926, 'end': 960},
    'user-management': {'start': 961, 'end': 1050},
    'vendors': {'start': 1051, 'end': 1170},
    'customers': {'start': 1171, 'end': 1250},
    'orders': {'start': 1251, 'end': 1350},
    'vendor-orders': {'start': 1351, 'end': 1455},
    'content': {'start': 1456, 'end': 1525},
    'messages': {'start': 1526, 'end': 1540},
    'reports': {'start': 1541, 'end': 1585},
    'settings': {'start': 1586, 'end': 1825},
}

# Create each section file
for name, bounds in sections.items():
    section_lines = lines[bounds['start']:bounds['end']]
    with open(f'resources/views/components/menu/sections/{name}.blade.php', 'w', encoding='utf-8') as f:
        f.writelines(section_lines)
    print(f"Created {name}.blade.php")

# Extract JavaScript (last 45 lines)
js_lines = lines[-45:]
with open('resources/views/components/menu/scripts.blade.php', 'w', encoding='utf-8') as f:
    f.writelines(js_lines)
print("Created scripts.blade.php")

print("\nAll sections created successfully!")
