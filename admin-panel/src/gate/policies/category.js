export default class Category
{
  static create(user)
  {
    return user.role === 'admin';
  }

  static update(user, category)
  {
    if (user.role === 'admin') {
      return true;
    }

    if (user.role === 'moderator' && user.category.id == category.id) {
      return true;
    }
    
    return false;
  }

  static delete(user)
  {
    return user.role === 'admin';
  }
}
