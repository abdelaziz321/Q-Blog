export default class Post
{
  static update(user, post)
  {
    return user.slug == post.author.slug;
  }

  static delete(user, post)
  {
    // admin
    if (user.role === 'admin') {
      return true;
    }

    // owner
    if (user.slug === post.author.slug) {
      return true;
    }

    // moderator
    if (user.role === 'moderator' && user.category.id === post.category.id) {
      return true;
    }

    return false;
  }

  static publish(user, post)
  {
    // admin
    if (user.role === 'admin') {
      return true;
    }

    // moderator
    if (user.role === 'moderator' && user.category.id === post.category.id) {
      return true;
    }

    return false;
  }

  static assignTags(user, post)
  {
    // admin
    if (user.role === 'admin') {
      return true;
    }

    // owner
    if (user.slug === post.author.slug) {
      return true;
    }

    // moderator
    if (user.role === 'moderator' && user.category.id === post.category.id) {
      return true;
    }

    return false;
  }
}
