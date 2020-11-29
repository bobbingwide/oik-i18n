```
template files ---- html2pot --------> theme.pot
& parts        -------.                  |
                      |                  |
                      |                l10n ( bb_BB )
                      |                  |
                   html2la_CY          theme-bb_BB.po
                      |                  |
                      |                msgfmt
languages/            |                  |
bb_BB/                |                  |           
template files <---------------------  theme-bb_BB.mo
& parts                                       
```
