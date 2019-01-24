## API Authentication

In the long term our webservices will need to have oAuth authentication. However
in the short term it is enough to have simple Basic Authentication using a Joomla
user account. However, users existing ACL rights must be respected when using the
API.

It is also possible to have a longer token associated with the account as an alternative
acceptable tradeoff between security and useability.

As per the section in [Extensibility](specification/chapters/extensibility.md) this
likely means some sort of integration
