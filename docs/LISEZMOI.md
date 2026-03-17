# Font Finder

Cette bibliothèque est un outil universel de recherche de polices : avec _presque tous_ les systèmes d'exploitation, pour _presque tous_ les formats de polices, renvoie un tableau d'informations standardisées. Elle se veut (très) rapide et récupère des informations minimales mais souvent suffisantes sur les polices.

Si vous appréciez ce package et souhaitez nous soutenir, ajoutez une étoile à ce projet : cela compte beaucoup pour nous !

## Exemple rapide

```php
$fonts = FontFileFinder::init()
    // Ajouter les polices système (dépend du système d'exploitation)
    ->addSystemFonts()
    // Ajouter un répertoire
    ->addDirectoryRecursive(implode(DIRECTORY_SEPARATOR, [__DIR__, "..", "tests", "samples"]))
    ->get();
```


Dans l'exemple précédent, `$fonts` est un tableau d'objets `Font`, regroupés par nom de police, comportant les propriétés suivantes :
- `filename` : emplacement de la police
- `name` : nom de la police
- `bold` : indicateur de gras (booléen)
- `italic` : indicateur italique (booléen)
- `weight` : poids du style (normalisation CSS), de 100 à 900

Pour en savoir plus, consultez la [documentation ci-dessous](#documentation).

## Installation

Ce package est disponible sur Composer. Pour l'installer :
```sh
composer require ls-a/xsl-core
```

## Systèmes d'exploitations pris en charge

Ce package a été testé avec :
- Windows x64
- Windows i386
- Windows ARM
- macOS amd64
- macOS arm64
- Linux amd64
- Linux arm64
- Linux armv7
- FreeBSD amd64
- FreeBSD arm64
- OpenBSD amd64
- OpenBSD arm64
- Solaris amd64

Plus plus d'informations, consultez l'[action test-and-bench](.github/workflows/test-and-bench.yml).

## Formats de polices pris en charge

This library currently retrieves font information in these formats:
- AdobeFontMetrics (*.afm, in conjuction with PFA/PFB/T1/PS)
- BitstreamSpeedo (*.spd)
- BsdVgaFont (*.fnt, but BSD ones, not Microsoft)
- CompactFontFormat (*.cff, both PostScript and binary)
- DataForkTrueTypeFont (*.dfont)
- EmbeddedOpenType (*.eot)
- GlyphBitmapDistributionFormat (*.bdf)
- JimHersheyFont (*.jhf)
- OpenTypeBitmap (*.otb)
- OpenTypeCollection (*.otc)
- OpenTypeFont (*.otf)
- PcScreenFont (*.psf)
- PcScreenFontUnicodeCompressed (*.psfu.gz, usually found with *.psf.fz extension)
- PcScreenFontCompressed (*.psf.gz)
- PcScreenFontUnicode (*.psfu)
- PortableCompiledFormat (*.pcf)
- PortableCompiledFormatCompressed (*.pcf.gz)
- PostScript (*.ps)
- PrinterFontAscii (*.pfa)
- PrinterFontBinary (*.pfb)
- PrinterFontMetrics (*.pfm, in conjuction with PFA/PFB/T1/PS)
- ScalableVectorGraphics (*.svg)
- ScalableVectorGraphicsCompressed (*.svgz)
- Type1 (*.t1)
- TrueTypeCollection (*.ttc)
- TrueTypeFont (*.ttf)
- WebOpenFontFormat (*.woff)
- WebOpenFontFormat2 (*.woff2)
- WindowsBitmapFontCollection (*.fon)

## Pourquoi ?

Ce devait être une toute petite partie d'un outil plus général, et finalement il y avait bien davantage à faire qu'escompté. Nous devions vérifier certaines options de configuration pour notre prochain package consacré à XSL-FO, en particulier l'option `<auto-detect/>` dans le fichier de configuration d'Apache FOP. Cette option permet de récupérer toutes les polices enregistrées dans le système d'exploitation actuel.  

Au départ, nous voulions valider certaines valeurs par rapport à cette option. Puis, nous avons constaté que les anciennes polices sur certains systèmes d'exploitation ne mentionnaient pas leur nom dans le nom de fichier. Et après quelques recherches sur Composer, il semble qu'aucun paquet ne le fasse. [Phenx PHP Font Lib](https://packagist.org/packages/phenx/php-font-lib) gère les formats TrueType, OpenType et WOFF, mais c'est tout. Et il est malheureusement très lent avec des centaines de polices.  

Au fil du temps et en fouillant dans l'histoire des polices, ce paquet est devenu au cours du développement un hommage à l'évolution des polices. Et c'est un plaisir de découvrir tant de façons d'intégrer des caractères et de la typographie.

Si la curiosité vous gagne, découvrez comment nous avons procédé [ici](docs/DEROULEMENT.md).

## Performance

_Si vous êtes ici, c'est que vous avez descendu l'historique des commits juste pour nous voir écrire ceci, pendant que les métriques étaient compilées sur le runner GitHub. Coucou!_

## Documentation

Chaque appel à ce package devrait être réalisé sur la classe `FontFileFinder`.

### Usage

#### Créer une instance: inst()

```php
// Renvoie une instance de FontFileFinder
$instance = FontFileFinder::inst();
```

Aucune opération ne sera effectuée tant que vous n'appelez pas la méthode `get()`.


#### Inclure les polices du système d'exploitation : addSystemFonts()

```php
// Renvoie une instance de FontFileFinder
$instance = FontFileFinder::inst()
    ->addSystemFonts();
```

Ajoutera les dossiers enregistrés par votre système pour le stockage des polices.

#### Inclure un dossier spécifique: addDirectory()

```php
// Renvoie une instance de FontFileFinder
$instance = FontFileFinder::inst()
    // Conseil : utilisez un chemin absolu
    ->addDirectory(implode(DIRECTORY_SEPARATOR, [
        __DIR__,
        'samples'
    ]));
```

Cette méthode ajoutera les polices présentes dans le dossier spécifié. Attention, cette méthode n'est pas récursive.

#### Inclure un dossier spécifique, de manière récursive: addDirectoryRecursive()

```php
// Renvoie une instance de FontFileFinder
$instance = FontFileFinder::inst()
    // Conseil : utilisez un chemin absolu
    ->addDirectoryRecursive(implode(DIRECTORY_SEPARATOR, [
        __DIR__,
        'samples'
    ]));
```

Cette méthode ajoutera les polices présentes dans le dossier spécifié, de manière récursive.

#### Exclure des chemins par motifs : except()

```php
$instance = FontFileFinder::inst()
    // Exclure les fichiers correspondant à l'expression régulière (ex: .gitignore, .gitkeep)
    ->except('/\.git.*$/')
    // Vous pouvez aussi préciser vos motifs avec une liste
    ->except([
        '/myfolder\/*/',
        '/\.php$/'
    ]) ;
```

Except method accepte des expressions régulières ; celles-ci sont exécutées sur le chemin absolu du fichier.
Si vous préférez exclude via des chaînes de caractères, utilisez exceptExact.


#### Exclure des noms de fichier: exceptExact()

```php
$instance = FontFileFinder::inst()
    // Exclure les fichiers .gitignore
    ->exceptExact('.gitignore')
    // Vous pouvez aussi préciser vos fichiers avec une liste
    ->exceptExact([
        '.DS_Store',
        '.gitkeep'
    ]);
```

Cette méthode compare les chaînes de caractères spécifiées avec le chemin absolu des polices, **avec la méthode `str_ends_with`**. Cela signifie que vous pouvez exclure des extensions sans préciser des expressions régulières, qui sont souvent plus lentes.

#### Récupérer les polices

```php
// Récupérer les polices
$fonts = FontFileFinder::init()
    // Ajoute les polices de caractères du système
    ->addSystemFonts()
    // Ajoute un dossier
    ->addDirectoryRecursive(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'tests', 'samples']))
    ->get();
```

Vous pouvez également récupérer d'autres informations: le nombre de polices, et les erreurs. La méthode `get()` method permet de spécifier les données que vous souhaitez :

```php
[$errors, $count, $fonts] = FontFileFinder::init()
    ->addSystemFonts()
    ->get('errors', 'count', 'fonts');
```

Pour le moment, seuls `errors`, `count` et `fonts` sont autorisés. Vous pouvez les préciser dans l'ordre de votre choix :

```php
[$count, $errors] = FontFileFinder::init()
    ->addSystemFonts()
    ->get('count', 'errors');
```

### Configuration

#### Cacher les erreurs

Par défaut, les erreurs sont affichées dans la sortie d'erreurs (stderr). Pour ne pas les afficher :

```php
$instance = FontFileFinder::inst()
    ->silent();
```

#### Activer les métriques de performance

Des fois, vous voulez voir les durées de traitement.

```php
$instance = FontFileFinder::inst()
    ->enableMetrics();
```

Vous pouvez les désactiver avec `disableMetrics`.

### Avancé

#### Utiliser un tableau de configuration

```php
$fonts = FontFileFinder::load([
     'directories' => [
         [
             'recursive' => true,
             'path' => '/usr/local/...'
         ],
         '/home/user/...'
     ]
]);
```

La méthode `load` accepte `autoDetect` (booléen) et `directories`. Ce n'est pas possible de récupérer les erreurs et le nombre de polices de cette manière : peut-être dans une prochaine version !

#### Vérifier les polices du système

Si vous voulez voir rapidement les polices du système d'exploitation, écrivez simplement :

```php
$fonts = FontFileFinder::getSystemFonts();
```

Vous n'aurez pas accès aux options de configuration de FontFileFinder, mais pour certains cas, c'est totalement suffisant.

#### Surcharger FontDecoder

FontFileFinder se base sur une classe nommée `FontDecoder`, qui:
- Exécute tous les décodeurs jusqu'à en trouver un correspondant au fichier de police actuel
- Crée les métriques de performance

Si cette classe n'est pas suffisante pour vos besoins, créez une nouvelle classe étendant FontDecoder, et déclaez-la comme suit :

```php
$instance = FontFileFinder::inst()
    ->setDecoderClass(MyAwesomeClass::class);
```

## Journal des modifications

Veuillez consulter le fichier [CHANGELOG](CHANGELOG.md) pour voir les dernières modifications.

## Support

Nous mettons du coeur à l'ouvrage pour proposer des produits de qualité et accessibles à toutes et tous. Si vous aimez notre travail, n'hésitez pas à faire appel à nous pour votre prochain projet !  

## Contributions

Les contributions sont régies par le fichier [CONTRIBUTING](https://github.com/ls-a-fr/.github/CONTRIBUTING.md).

## Sécurité

Si vous avez déniché un bug ou une faille, merci de nous contacter par mail à [mailto:contact@ls-a.fr](contact@ls-a.fr) en lieu et place d'une issue, pour respecter la sécurité des autres usagers.


## Crédits

- Renaud Berthier

## Licence

Code déposé sous licence MIT. Rendez-vous sur le fichier LICENSE pour davantage d'informations.