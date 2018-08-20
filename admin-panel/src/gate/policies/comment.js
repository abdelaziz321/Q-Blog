export default class Comment
{
  static delete(user, comment)
  {
    // owner
    if (user.slug === comment.user.slug) {
      return true;
    }

    // admin
    if (user.role === 'admin') {
      return true;
    }

    // author
    if (user.role === 'author' && comment.post.author === user.id)
    {
      return true;
    }

    // moderator
    if (user.role === 'moderator' && user.category.id === comment.post.category) {
      return true;
    }

    return false;
  }
}
