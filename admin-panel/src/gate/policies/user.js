export default class User
{
  static assignRole(user)
  {
    return user.role === 'admin';
  }

  static delete(user, deleted)
  {
    // admin
    if (user.role === 'admin') {
      return true;
    }

    // delete himself :)
    if (user.id === deleted.id) {
      return true;
    }

    return false;

  }
}
