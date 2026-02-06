# Auto Email WordPress Plugin

By: DoroteoDigital

This project uses PHP version 8.4.

<hr>

This repository is meant to showcase a private WordPress plugin that is in active development.
This plugin exposes REST endpoints to be used in an external application for sending automatic email notifications to users and site owners.

The main use for this plugin is to work with a React website to add functionality to contact/booking forms.

<hr>

Directory Structure:
```
src/
├── admin -------------------------------------- Handles plugin admin menu. 
│   ├── AjaxHandler.php
│   ├── PluginOptions.php
│   ├── SettingsPage.php
│   ├── templates
│   └── vars.php
├── api ---------------------------------------- Handles REST API endpoints.
│   ├── Api.php
│   └── utils
├── frontend ----------------------------------- React app for admin menu.
│   ├── components
│   ├── hooks
│   ├── index.css
│   ├── lib
│   ├── SettingsPage.tsx
│   └── types
├── index.tsx
├── parser ------------------------------------- Depreciated. Use Templates class instead.
│   └── Parser.php
├── sender ------------------------------------- Depreciated. Use REST endpoints instead.
│   └── Sender.php
└── templates ---------------------------------- Handles parsing and filling html templates.
    ├── client-confirmation.template.html
    ├── client-contact-form.template.html
    ├── exceptions
    ├── general-template.html
    ├── general-template-mso.html
    ├── owner-contact-form.template.html
    ├── owner-registration-confirmation.template.html
    └── Templates.php
```
