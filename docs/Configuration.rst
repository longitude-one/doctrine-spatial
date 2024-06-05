Configuration
=============

Configuration for applications using Symfony framework
------------------------------------------------------
To configure Doctrine spatial extension on your Symfony application, simply edit your ``config/doctrine.yaml``
file. This process involves two steps:

* **Declaring Spatial Types for DQL:** This step allows you to use spatial data types directly within your Doctrine Query Language (DQL) queries.
* **Declaring Spatial Functions:** This step enables you to leverage the extension's spatial functions within your DQL queries to perform operations on your spatial data.

These two configuration steps empower you to effectively manage spatial entities within your Symfony application.

Declare your geometric types
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: yaml

    #config/doctrine.yaml
    doctrine:
        dbal:
            types:
                geometry:   LongitudeOne\Spatial\DBAL\Types\GeometryType
                point:      LongitudeOne\Spatial\DBAL\Types\Geometry\PointType
                polygon:    LongitudeOne\Spatial\DBAL\Types\Geometry\PolygonType
                linestring: LongitudeOne\Spatial\DBAL\Types\Geometry\LineStringType

Now, you can :doc:`create an entity <./Entity>` with a ``geometry``, ``point``, ``polygon`` and a ``linestring`` type.

Here is a complete example showcasing all available spatial types supported by the Doctrine spatial extension.
Notably, the names of these Doctrine types are not hardcoded. This means you have flexibility.
If you only intend to use geometric types, you can simply remove the ``geometrY_`` prefixes from the configuration.
This customization allows you to tailor the extension's functionality to your specific needs.

.. code-block:: yaml

    doctrine:
        dbal:
            types:
                geography:            LongitudeOne\Spatial\DBAL\Types\GeographyType
                geography_linestring: LongitudeOne\Spatial\DBAL\Types\Geography\LineStringType
                geography_point:      LongitudeOne\Spatial\DBAL\Types\Geography\PointType
                geography_polygon:    LongitudeOne\Spatial\DBAL\Types\Geography\PolygonType

                geometry:            LongitudeOne\Spatial\DBAL\Types\GeometryType
                geometry_linestring: LongitudeOne\Spatial\DBAL\Types\Geometry\LineStringType
                geometry_point:      LongitudeOne\Spatial\DBAL\Types\Geometry\PointType
                geometry_polygon:    LongitudeOne\Spatial\DBAL\Types\Geometry\PolygonType
                geometry_multilinestring: LongitudeOne\Spatial\DBAL\Types\Geometry\MultiLineStringType
                geometry_multipoint:      LongitudeOne\Spatial\DBAL\Types\Geometry\MultiPointType
                geometry_multipolygon:    LongitudeOne\Spatial\DBAL\Types\Geometry\MultiPolygonType

I strive to keep this documentation up-to-date. However, if you ever encounter any discrepancies,
you can refer directly to the source code. The `DBAL/Types`_ directory within the project repository contains all
currently implemented geometric and geographic spatial types. This allows you to verify the latest available types.

We welcome your contributions to expand the range of supported spatial types!
The `Open Geospatial Consortium (OGC) standard`_ and the `ISO/IEC 13249-3:2016`_ standard define various spatial types
beyond the currently implemented ones, such as ``Curve`` and ``PolyhedSurface``. If you're interested in helping implement these
additional types, please refer to the contribution :doc:`guide <./Contributing>`.

Declare a new function
^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: yaml

    orm:
        dql:
            numeric_functions:
                #Declare functions returning a numeric value
                #A good practice is to prefix functions with ST when they are issue from the Standard directory
                st_area: LongitudeOne\Spatial\ORM\Query\AST\Functions\Standard\StArea
            string_functions:
                #Declare functions returning a string
                st_envelope: LongitudeOne\Spatial\ORM\Query\AST\Functions\Standard\STEnvelope
                #Prefix functions with SP when they are not issue from the Standard directory is a good practice
                sp_asgeojson: LongitudeOne\Spatial\ORM\Query\AST\Functions\Postgresql\SpAsGeoJson
                #You can use the DQL function name you want and then use it in your DQL
                myDQLFunctionAlias: LongitudeOne\Spatial\ORM\Query\AST\Functions\Standard\StCentroid
                #SELECT myDQLFunctionAlias(POLYGON(...

Add only the functions you want to use. The list of available function can be found in these sections:

1. list of :ref:`Standard functions` declared in the `Open Geospatial Consortium standard`_,
2. list of :ref:`Specific PostGreSQL functions` which are not already declared in the OGC Standard,
3. list of :ref:`Specific MySQL functions` which are not already declared in the OGC Standard,

Nota: By default, function declared by the `Open Geospatial Consortium`_ in the `standards of SQL Options`_ are prefixed
by ``ST_``, other functions should not be declared with this prefix. We suggest to use the ``SP_`` prefix (specific).

Configuration for other application
-----------------------------------

Declare your geometric types
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Doctrine provides the ability to create custom mapping types.
This functionality is precisely what the Doctrine spatial extension utilizes to define its spatial data types.

To instruct Doctrine on the type you want to use, only two arguments of configuration are required.
The first one references the Type class, and the second one declares the specific type itself.
For example, the code below demonstrates declaring a geometric "point" type with the ``addType`` static method:

.. code-block:: php

    <?php
    // in your bootstrapping code

    // ...

    // This class is provided by the Doctrine library
    use Doctrine\DBAL\Types\Type;

    // ...

    // Register types implemented by the doctrine2 spatial extension
    Type::addType('point', 'LongitudeOne\Spatial\DBAL\Types\Geometry\PointType');

Declare a new function
^^^^^^^^^^^^^^^^^^^^^^

You can register functions provided by the Doctrine spatial extension by adding them to your ORM configuration.

.. code-block:: php

    <?php

    // in your bootstrapping code

    // ...

    // This class is implemented by the Doctrine library
    use Doctrine\ORM\Configuration\Doctrine\ORM\Configuration;

    // ...

    $config = new Configuration();
    // This is an example to declare a standard spatial function which is returning a string
    $config->addCustomStringFunction('ST_Envelope', 'LongitudeOne\Spatial\ORM\Query\AST\Functions\Standard\StEnvelope');
    // This is another example to declare a standard spatial function which is returning a numeric
    $config->addCustomNumericFunction('ST_Area', 'LongitudeOne\Spatial\ORM\Query\AST\Functions\Standard\StArea');
    // This is another example to declare a Postgresql specific function which is returning a string
    $config->addCustomNumericFunction('SP_GeoJson', 'LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql\SpGeoJson');

Coordinates order
-----------------

In point constructor, the order is the same as the spatial database.
It means:
 * longitude shall be set before latitude in point constructor,
 * X shall be set before Y.

.. _ISO/IEC 13249-3:2016: https://www.iso.org/standard/60343.html
.. _Open Geospatial Consortium: https://www.ogc.org/
.. _Open Geospatial Consortium (OGC) standard: https://www.ogc.org/standards/sfs
.. _standards of SQL Options: https://www.ogc.org/standards/sfs
.. _DBAL/Types: https://github.com/longitude-one/doctrine-spatial/tree/master/lib/LongitudeOne/Spatial/DBAL/Types