.. Doctrine Spatial extension documentation main file, created by Alexandre Tranchant

Welcome to Doctrine-Spatial extension's documentation!
######################################################

Doctrine-Spatial extension provides spatial types and spatial functions for doctrine. It allows you to manage
spatial entities and to store them into your database server.

Currently, Doctrine-Spatial extension supports two-dimension geometric and geographic spatial types.
These include points, linestrings, polygons, and their multi-dimensional counterparts
(multi-points, multi-linestrings, and multi-polygons). It supports MySQL, MariaDB, PostgreSQL/PostGIS, and SQL Server.

This project was initially created by Derek J. Lambert in 2015. In March 2020, Alexandre Tranchant forked the originally
project due to inactivity for two years. We welcome contribution (see the contribution :doc:`guide <./Contributing>`.

Full example
------------

.. note::

   The `Doctrine-Spatial demo <https://github.com/longitude-one/doctrine-spatial-demo>`_ is a complete web application
   built with Symfony, Doctrine, and Doctrine-Spatial. It illustrates a concrete use case for spatial queries:
   are our superheroes really in Gotham City?

Contents
********

.. toctree::
   :maxdepth: 5

   Installation
   Configuration
   Entity
   Repository
   Glossary
   Contributing
   Test
