## Business Models

- *Current JModels*: The state of the models is currently tightly coupled (populate state etc.) to web stuff. Not channel-agnostic. PRO: They are thoroughly tested. For example: High level hacks for simple read operations. [https://github.com/mbabker/jdayflorida-app/tree/master/libraries/api/controller](https://github.com/mbabker/jdayflorida-app/tree/master/libraries/api/controller)

Following the previous arguments, in both cases, **JModels** are the best available business layer to integrate the upper layers.

In the future a *Mini-Service Layer* could help to create a clean layer. E.g. Article management via JTable, featured and frontend tables.

In this project, we will implement a simple serialization. Entity access level - Serialization from a model's getItem()

Topics to be checked: Tags, Version history, JForms, Custom Fields, Rules, and Filters.

* *Interfaces (TBD)*:
  - JModelInterface
    - getItem
    - getItems  
    - ...  
