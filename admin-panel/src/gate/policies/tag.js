export default class Tag
{
  static update(user)
  {
    return user.role === 'admin';
  }

  static delete(user)
  {
    return user.role === 'admin';
  }
}
