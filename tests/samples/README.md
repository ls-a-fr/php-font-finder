# Introduction

Files in this `samples` directory are taken from a lot of sources. This file:
- lists every source
- thanks everyone
- gives information if you feel, as a license owner, this directory is a breach of intellectual property

We checked every license given with every file, when available, and had best intentions while creating this directory, but a mistake is always possible.

# Samples, organized by folders

## bdf

- Font extensions: `*.bdf`
- Source : [https://www.x.org/releases/individual/font/](https://www.x.org/releases/individual/font/), package `font-daewoo-misc-1.0.0`

Thanks X.org for their packages and archives!

## bsd-fnt

- Font extensions: `*.fnt`
- Source: [https://github.com/usonianhorizon/vt-fnt/blob/master/fnt/IBMPlexMono-Light/](https://github.com/usonianhorizon/vt-fnt/blob/master/fnt/IBMPlexMono-Light/)

Thanks @usonianhorizon for providing these fonts!

## cff

- Font extension: `*.cff`
- Source: [https://github.com/fonttools/fonttools](https://github.com/fonttools/fonttools): TestSupplementEncoding


Thanks @fonttools for providing these fonts!

## cffps

- Font extension: `*.cff` (PostScript embedded)
- Source: [https://codeberg.org/m-casanova/Garamontio/releases/download/v1.091/garamontio_otf.zip](https://codeberg.org/m-casanova/Garamontio/releases/download/v1.091/garamontio_otf.zip): Garamontio-Bold.cff

Thanks @m-casanova for providing these fonts!
 
## dfont

- Font extension: `*.dfont`
- Sources:
    - [https://github.com/roman0x58/tamsyn-mac-version/tree/master](https://github.com/roman0x58/tamsyn-mac-version/tree/master): Tamsyn* and Tamzen*
    - [https://github.com/PeterUpfold/DfontSplitter](https://github.com/PeterUpfold/DfontSplitter): PTSans-regular.otf.dfont

Thanks @PeterUpfold and @roman0x58 for providing these fonts!

## eot

- Font extension: `*.eot`
- Sources:
    - [https://github.com/FortAwesome/Font-Awesome/blob/4.x/fonts/](https://github.com/FortAwesome/Font-Awesome/blob/4.x/fonts/): fontawesome-webfont
    - [https://github.com/twbs/bootstrap/blob/v3-dev/fonts/](https://github.com/twbs/bootstrap/blob/v3-dev/fonts/): glyphicons-halflings-regular
    - [https://github.com/iconic/open-iconic/blob/master/font/fonts/](https://github.com/iconic/open-iconic/blob/master/font/fonts/): open-iconic
    - [https://github.com/stefan6419846/eot_tools/blob/main/tests/files/](https://github.com/stefan6419846/eot_tools/blob/main/tests/files/): Maki
    - [https://github.com/Azure-Samples/tutor/blob/main/frontend/fonts/](https://github.com/Azure-Samples/tutor/blob/main/frontend/fonts/): Satoshi*

Thanks @FortAwesome, @twbs, @iconic, @stefan6419846 and @Azure-Samples for providing these fonts!

## fon

- Font extension: `*.fon`
- Sources:
    - [https://github.com/fcambus/spleen/releases/tag/2.2.0](https://github.com/fcambus/spleen/releases/tag/2.2.0)
    - [https://www.chiark.greenend.org.uk/~sgtatham/fonts/](https://www.chiark.greenend.org.uk/~sgtatham/fonts/)
Thanks @fcambus for providing these fonts! Thanks Simon Tatham for providing these fonts and for the discussion about them!

## jhf

- Font extension: `*.jhf`
- Source: [https://github.com/kamalmostafa/hershey-fonts/tree/master](https://github.com/kamalmostafa/hershey-fonts/tree/master)

Thanks @kamalmostafa for providing these fonts!

## otb

- Font extension: `*.otb`
- Source: [https://int10h.org/oldschool-pc-fonts/download/](https://int10h.org/oldschool-pc-fonts/download/)

Thanks int10h for providing these fonts!

## otc

- Font extension: `*.otc`
- Source: [https://github.com/notofonts/noto-cjk/blob/main/Sans/OTC](https://github.com/notofonts/noto-cjk/blob/main/Sans/OTC)

Thanks @notofonts for providing these fonts!

## otf

- Font extension: `*.otf`
- Source: [https://github.com/notofonts/notofonts.github.io/tree/main/fonts](https://github.com/notofonts/notofonts.github.io/tree/main/fonts)

Thanks @notofonts for providing these fonts!

## pcf

- Font extension: `*.pcf`
- Source: `apt install xfonts-100dpi & xfonts-75dpi`

Thanks for providing these packages!

## pcf-gz

- Font extension: `*.pcf.gz`
- Source: `apt install xfonts-100dpi & xfonts-75dpi`

Thanks for providing these packages!

## pfa

- Font extension: `*.pfa`
- Source: [https://mirrors.slackware.com/slackware/slackware-3.3/slakware](https://mirrors.slackware.com/slackware/slackware-3.3/slakware), folder `/ap5/gsfonts3.tgz`

Thanks Slackware for their packages and archives, thanks GhostScript for providing these fonts!

## pfb

- Font extensions: `*.afm` (metrics), `*.pfm` (metrics), `*.pfb`
- Sources:
    - [https://www.x.org/releases/individual/font/](https://www.x.org/releases/individual/font/), package `font-bitstream-type1-1.0.4`
    - [https://mirrors.slackware.com/slackware/slackware-3.3/slakware](https://mirrors.slackware.com/slackware/slackware-3.3/slakware) `/ap3/gsfonts1.tgz`

Thanks X.org and Slackware for their packages and archives, thanks GhostScript for providing these fonts!

## ps

- Font extension: `*.ps`

Done by converting `*.pfa` files from this repository into `*.ps` fonts with this command:
```sh
for f in *.pfa; do echo '%!PS-AdobeFont-1.0' > "${f%.pfa}.ps"; cat "$f" >> "${f%.pfa}.ps"; done
```

## psf

- Font extension: `*.psf`

Done by converting `*.psfu` files from this repository into `*.psf` fonts with this command:
```sh
for f in *.psf; do psfstriptable "$f" "${f%.psf}-no-unicode.psf"; don
```

## psf-gz

- Font extension: `*.psf.gz`

Gunzip version of psf fonts, with this command:
```sh
for f in *.psf; do gzip -c "$f" > "$f.gz"; done
```

## psfu

- Font extension: `*.psfu`,  `*.psf`
- Sources:
    - [https://github.com/legionus/kbd/blob/master/data/consolefonts](https://github.com/legionus/kbd/blob/master/data/consolefonts): *.psfu files
    - [https://github.com/ercanersoy/PSF-Fonts](https://github.com/ercanersoy/PSF-Fonts): *.psf files except Solarize
    - [https://github.com/talamus/solarize-12x29-psf](https://github.com/talamus/solarize-12x29-psf): Solarize.12x29.psf

Thanks @legionus, @ercanersoy and @talamus for providing these fonts!
Note: `*.psf` files are actually in PSFU format.

## psfu-gz

- Font extension: `*.psfu.gz`

Gunzip version of psfu fonts, with this command:
```sh
for f in *.psf; do gzip -c "$f" > "$f.gz"; done
```

## spd

- Font extension: `*.spd`
- Sources:
    - [https://www.x.org/releases/individual/font/](https://www.x.org/releases/individual/font/), package `font-bitstream-speedo-1.0.2.tar.gz`
    - [https://web.archive.org/web/20160809100244/http://www.xywrite.com/speedo/speedos.zip](https://web.archive.org/web/20160809100244/http://www.xywrite.com/speedo/speedos.zip)


Thanks X.org for their packages and archives, thanks xywrite for providing these fonts, and thanks WebArchive for letting us still access resources after a website disappearance.

## svg

- Font extension: `*.svg`
- Sources:
    - [https://github.com/FortAwesome/Font-Awesome/blob/4.x/fonts/](https://github.com/FortAwesome/Font-Awesome/blob/4.x/fonts/): fontawesome-webfont.svg
    - [https://github.com/twbs/bootstrap/blob/v3-dev/fonts/](https://github.com/twbs/bootstrap/blob/v3-dev/fonts/): glyphicons-halflings-regular.svg
    - [https://github.com/iconic/open-iconic/blob/master/font/fonts/](https://github.com/iconic/open-iconic/blob/master/font/fonts/): open-iconic.svg

Thanks @FortAwesome, @twbs, @iconic, @stefan6419846 and @Azure-Samples for providing these fonts!

## svgz

- Font extension: `*.svgz`

Obtained by using `gzip *.svg` on this repository

## t1

- Font extension: `*.t1`
- Sources: `apt install ghostscript`

Thanks GhostScript for providing these fonts!

## ttc-apple

- Font extension: `*.ttc`
- Source: macOS

These fonts have strict license and thus are not available on this repository.

## ttc-microsoft

- Font extension: `*.ttc`
- Source: Windows

These fonts have strict license and thus are not available on this repository.

## ttf

- Font extension: `*.ttf`
- Source: [https://software.sil.org]

Thanks SIL Global for these fonts and for SIL Open Font License!

## woff

- Font extension: `*.woff`
- Source: [https://software.sil.org]

Thanks SIL Global for these fonts and for SIL Open Font License!

## woff2

- Font extension: `*.woff2`
- Source: [https://software.sil.org]

Thanks SIL Global for these fonts and for SIL Open Font License!

# I want my font to be removed from this list

Please contact us by using [our contact form](https://ls-a.fr/contact). We only read messages written in french or in english (because we can only read these). If you can't write in any of these languages, please use a translation tool before submitting your message.  

In your message, we would like you to supply:
- File license
- If you aren't intellectual property holder, and if you can, a direct contact to people who do
- Why us pushing your font through this repository breaches your license
- Maybe a link to other font we could use in place of yours, to help us maintain a sufficient coverage
- And any other meaningful details

This project is about helping others, not inconvenience anyone: we'll read you and make our best efforts.