import TagPolicy from './policies/tag.js';
import UserPolicy from './policies/user.js';
import PostPolicy from './policies/post.js';
import CommentPolicy from './policies/comment.js';
import CategoryPolicy from './policies/category.js';

export default class Gate
{
  constructor()
  {
    this.policies = {
      tag: TagPolicy,
      user: UserPolicy,
      post: PostPolicy,
      comment: CommentPolicy,
      category: CategoryPolicy
    };
  }

  setUser(user)
  {
    this.user = user;
  }

  allow(action, type, model = null)
  {
    return this.policies[type][action](this.user, model);
  }

  deny(action, type, model = null)
  {
    return ! this.allow(action, type, model);
  }
}
