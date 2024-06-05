.. Doctrine spatial extension documentation master file, created by Alexandre Tranchant

Welcome to Doctrine-Spatial extension's documentation!
######################################################

Doctrine-Spatial extension provides spatial types and spatial functions for doctrine. It allows you to manage
spatial entities and to store them into your database server.

Currently, Doctrine-Spatial extension supports two-dimension geometric and geographic spatial types.
These include points, linestrings, polygons, and their multi-dimensional counterparts
(multi-points, multi-linestrings, and multi-polygons). It is compatible with MySQL and PostgreSQL databases.

This project was initially created by Derek J. Lambert in 2015. In March 2020, Alexandre Tranchant forked the originally
project due to inactivity for two years. We welcome contribution (see the contribution :doc:`guide <./Contributing>`.

Here are some areas where your help would be appreciated:

* Implementing support for third and fourth dimensions in spatial data,
* Implementing new spatial functions,
* Improving documentation by completing it and fixing typos *(even if your English isn't perfect, we can still use your help!)*
* Implementing support for new database platforms, such as Microsoft SQL Server.

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

