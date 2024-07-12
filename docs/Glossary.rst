Glossary
********

Types
=====

.. _Standard types:

Types described in OGC Standards or in ISO/IEC 13249-3:2016
-----------------------------------------------------------

The `ISO/IEC 13249-3`_ International Standard defines multimedia and application specific types and their
associated routines using the user-defined features in ISO/IEC 9075. The third part of ISO/IEC 13249 defines spatial
user-defined types and their associated routines.

In doctrine spatial extensions, some of all normalized spatial user-defined types are implemented.

This section lists them.

+------------------------+-------------+-------------+----------+------------+
| Spatial types          | COORDINATES | Implemented | MySql    | PostgreSql |
+========================+=============+=============+==========+============+
| Geometric              |    X, Y     |     YES     |    YES   |     YES    |
+------------------------+-------------+-------------+----------+------------+
| Point                  |    X, Y     |     YES     |    YES   |     YES    |
+------------------------+-------------+-------------+----------+------------+
| LineString             |    X, Y     |     YES     |    YES   |     YES    |
+------------------------+-------------+-------------+----------+------------+
| Polygon                |    X, Y     |     YES     |    YES   |     YES    |
+------------------------+-------------+-------------+----------+------------+
| MultiPoint             |    X, Y     |     YES     |    YES   |     YES    |
+------------------------+-------------+-------------+----------+------------+
| MultiLineString        |    X, Y     |     YES     |    YES   |     YES    |
+------------------------+-------------+-------------+----------+------------+
| MultiPolygon           |    X, Y     |     YES     |    YES   |     YES    |
+------------------------+-------------+-------------+----------+------------+
| GeomCollection         |    X, Y     |     NO      |          |            |
+------------------------+-------------+-------------+----------+------------+
| Curve                  |    X, Y     |   NI [#NI]_ | NI [#NI]_| NI [#NI]_  |
+------------------------+-------------+-------------+----------+------------+
| Surface                |    X, Y     |   NI [#NI]_ | NI [#NI]_| NI [#NI]_  |
+------------------------+-------------+-------------+----------+------------+
| PolyHedralSurface      |    X, Y     |     NO      |    NO    |     NO     |
+------------------------+-------------+-------------+----------+------------+
| GeometricZ             |  X, Y, Z    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| PointZ                 |  X, Y, Z    |  Incoming   |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| LineStringZ            |  X, Y, Z    |  Incoming   |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| PolygonZ               |  X, Y, Z    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| MultiPointZ            |  X, Y, Z    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| MultiLineStringZ       |  X, Y, Z    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| MultiPolygonZ          |  X, Y, Z    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| GeomCollectionZ        |  X, Y, Z    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| CurveZ                 |  X, Y, Z    |   NI [#NI]_ | NI [#NI]_| NI [#NI]_  |
+------------------------+-------------+-------------+----------+------------+
| SurfaceZ               |  X, Y, Z    |   NI [#NI]_ | NI [#NI]_| NI [#NI]_  |
+------------------------+-------------+-------------+----------+------------+
| PolyHedralSurfaceZ     |  X, Y, Z    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| GeometricM             |  X, Y, M    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| PointM                 |  X, Y, M    |  Incoming   |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| LineStringM            |  X, Y, M    |  Incoming   |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| PolygonM               |  X, Y, M    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| MultiPointM            |  X, Y, M    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| MultiLineStringM       |  X, Y, M    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| MultiPolygonM          |  X, Y, M    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| GeomCollectionM        |  X, Y, M    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| CurveM                 |  X, Y, M    |   NI [#NI]_ | NI [#NI]_| NI [#NI]_  |
+------------------------+-------------+-------------+----------+------------+
| SurfaceM               |  X, Y, M    |   NI [#NI]_ | NI [#NI]_| NI [#NI]_  |
+------------------------+-------------+-------------+----------+------------+
| PolyHedralSurfaceM     |  X, Y, M    |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| GeometricZM            | X, Y, Z, M  |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| PointZM                | X, Y, Z, M  |  Incoming   |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| LineStringZM           | X, Y, Z, M  |  Incoming   |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| PolygonZM              | X, Y, Z, M  |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| MultiPointZM           | X, Y, Z, M  |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| MultiLineStringZM      | X, Y, Z, M  |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| MultiPolygonZM         | X, Y, Z, M  |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| GeomCollectionZM       | X, Y, Z, M  |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+
| CurveZM                | X, Y, Z, M  |   NI [#NI]_ | NI [#NI]_| NI [#NI]_  |
+------------------------+-------------+-------------+----------+------------+
| SurfaceZM              | X, Y, Z, M  |   NI [#NI]_ | NI [#NI]_| NI [#NI]_  |
+------------------------+-------------+-------------+----------+------------+
| PolyHedralSurfaceZM    | X, Y, Z, M  |     NO      |NA [#f1]_ |            |
+------------------------+-------------+-------------+----------+------------+

.. [#NI] Not instantiable : Some types are defined as not instantiable by `ISO/IEC 13249-3`_ International Standard
.. [#f1] Not applicable, MySQL doesn't support these types yet.

Functions
=========

.. _Standard functions:

Functions described in OGC Standards or in ISO/IEC 13249-3:2016
---------------------------------------------------------------

The `ISO/IEC 13249-3`_ International Standard defines multimedia and application specific types and their
associated routines using the user-defined features in ISO/IEC 9075. The third part of ISO/IEC 13249 defines spatial
user-defined types and their associated routines.

Associated routines of this document are considered as the "Standard functions" for this doctrine spatial extension.
I try to maintain this documentation up-to-date. In any case, you will find under the `Functions/Standards directory`_ a
set of classes. Each class implement the spatial function of the same name.

The below table shows the defined functions:

+------------------------+-------------+----------+----------+------------+
| Spatial functions      | Implemented | Type     | MySql    | PostgreSql |
+========================+=============+==========+==========+============+
| ST_Area                |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_AsBinary            |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Boundary            |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Buffer              |     YES     | Numeric  |    NO*   |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Centroid            |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Contains            |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_ConvexHull          |     YES     | String   |    NO    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Crosses             |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Difference          |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Dimension           |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Disjoint            |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Distance            |     YES     | Numeric  |    NO*   |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Equals              |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Intersects          |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Intersection        |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_IsClosed            |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_IsEmpty             |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_IsRing              |     YES     | Numeric  |    NO    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_IsSimple            |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_EndPoint            |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Envelope            |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_ExteriorRing        |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_GeometryN           |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_GeometryN           |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_EndPoint            |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_GeometryType        |     YES     | Numeric  |    NO*   |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_GeomFromWkb         |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_GeomFromText        |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_InteriorRingN       |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Length              |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_LineStringFromWkb   |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_MPointFromWkb       |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_MLineFromWkb        |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_MPolyFromWkb        |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_NumInteriorRing     |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_NumGeometries       |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_NumPoints           |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Overlaps            |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Perimeter           |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Point               |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_PointFromWkb        |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_PointN              |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_PointOnSurface      |     YES     | String   |    NO    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_PolyFromWkb         |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Relate              |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_SetSRID             |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_StartPoint          |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_SymDifference       |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Touches             |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Union               |     YES     | String   |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Within              |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_X                   |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+
| ST_Y                   |     YES     | Numeric  |   YES    |    YES     |
+------------------------+-------------+----------+----------+------------+

.. _Specific PostGreSQL functions:

Specific functions of the PostgreSql database server
----------------------------------------------------

If your application can be used with another database server than PostgreSql, you should avoid to use these functions.
It's a good practice to name function with the SP prefix, but do not forget that you can name
all functions as you want when you declare it into your configuration files or in your bootstrap.

+----------------------------------------+-------------+----------+
| Specific PostgreSQL Spatial functions  | Implemented | Type     |
+========================================+=============+==========+
| Sp_AsGeoJson                           |     YES     | String   |
+----------------------------------------+-------------+----------+
| Sp_Azimuth                             |     YES     | String   |
+----------------------------------------+-------------+----------+
| Sp_ClosestPoint                        |     YES     | String   |
+----------------------------------------+-------------+----------+
| Sp_Collect                             |     YES     | String   |
+----------------------------------------+-------------+----------+
| Sp_ContainsProperly                    |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_CoveredBy                           |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_Covers                              |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_Distance_Sphere                     |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_DWithin                             |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_Expand                              |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_GeogFromText                        |     YES     | String   |
+----------------------------------------+-------------+----------+
| Sp_GeographyFromText                   |     YES     | String   |
+----------------------------------------+-------------+----------+
| Sp_GeomFromEwkt                        |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_GeometryType                        |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_LineCrossingDirection               |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_LineSubstring                       |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_LineLocatePoint                     |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_LineInterpolatePoint                |     YES     | String   |
+----------------------------------------+-------------+----------+
| Sp_MakeEnvelope                        |     YES     | String   |
+----------------------------------------+-------------+----------+
| Sp_MakeBox2D                           |     YES     | String   |
+----------------------------------------+-------------+----------+
| Sp_MakeLine                            |     YES     | String   |
+----------------------------------------+-------------+----------+
| Sp_MakePoint                           |     YES     | String   |
+----------------------------------------+-------------+----------+
| Sp_NPoints                             |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_Scale                               |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_Simplify                            |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_Split                               |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_SnapToGrid                          |     YES     | String   |
+----------------------------------------+-------------+----------+
| Sp_Summary                             |     YES     | String   |
+----------------------------------------+-------------+----------+
| Sp_Transform                           |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_Translate                           |     YES     | Numeric  |
+----------------------------------------+-------------+----------+


.. _Specific MySql functions:

Specific functions of the MySql database server
----------------------------------------------------
If your application can be used with another database server than MySql, you should avoid to use these functions.

+----------------------------------------+-------------+----------+
| Specific MySQL Spatial functions       | Implemented | Type     |
+========================================+=============+==========+
| Sp_Distance                            |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_Buffer                              |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_BufferStrategy                      |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_Distance_Sphere                     |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_GeometryType                        |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_LineString                          |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_MBRContains                         |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_MBRDisjoint                         |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_MBREquals                           |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_MBRDisjoint                         |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_MBRIntersects                       |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_MBROverlaps                         |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_MBRTouches                          |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_MBRWithin                           |     YES     | Numeric  |
+----------------------------------------+-------------+----------+
| Sp_Point                               |     YES     | Numeric  |
+----------------------------------------+-------------+----------+

Nota: Since MySql 5.7, a lot of functions are deprecated. These functions have been removed from doctrine spatial
extensions, because they are replaced by their new names. As example, the GeomFromText function does no more exist. It
has been replaced by the Standard function ST_GeomFromText since MySql 5.7. So if you was using GeomFromText, removed
it and use the standard function declared in the StGeomFromText class.

.. _ISO/IEC 13249-3: https://www.iso.org/standard/60343.html
.. _Functions/Standards directory: https://github.com/longitude-one/doctrine-spatial/tree/master/lib/LongitudeOne/Spatial/ORM/Query/AST/Functions/Standard

